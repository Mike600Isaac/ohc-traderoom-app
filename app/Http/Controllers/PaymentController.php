<?php

namespace App\Http\Controllers;

use App\Models\MemberEntitlement;
use App\Models\PaymentTransaction;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentController extends Controller
{
    public function show(string $course)
    {
        return view('payments.confirm', [
            'course' => $this->courseConfig($course),
        ]);
    }

    public function start(Request $request, PaystackService $paystack, string $course): RedirectResponse
    {
        $courseConfig = $this->courseConfig($course);
        $user = $request->user();
        $reference = 'ohc_'.Str::lower(Str::random(18));

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'course_id' => $courseConfig['id'],
            'course_name' => $courseConfig['name'],
            'amount' => $courseConfig['amount'],
            'currency' => $courseConfig['currency'],
            'reference' => $reference,
            'status' => 'pending',
            'metadata' => [
                'course_id' => $courseConfig['id'],
                'course_name' => $courseConfig['name'],
                'offer_type' => $courseConfig['offer_type'],
                'user_id' => $user->id,
            ],
        ]);

        if ((int) $courseConfig['amount'] <= 0) {
            DB::transaction(function () use ($transaction) {
                $entitlement = $this->grantAccess($transaction);

                $transaction->update([
                    'member_entitlement_id' => $entitlement->id,
                    'status' => 'success',
                    'gateway_response' => 'Free course access granted.',
                    'paid_at' => now(),
                ]);
            });

            return redirect()
                ->route('dashboard')
                ->with('payment_success', 'Your free course access has been activated.');
        }

        try {
            $response = $paystack->initialize([
                'email' => $user->email,
                'amount' => $courseConfig['amount'],
                'currency' => $courseConfig['currency'],
                'reference' => $reference,
                'callback_url' => route('payments.callback'),
                'metadata' => [
                    'course_id' => $courseConfig['id'],
                    'course_name' => $courseConfig['name'],
                    'user_id' => $user->id,
                ],
            ]);
        } catch (RuntimeException $exception) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('home')
                ->with('payment_error', $exception->getMessage());
        }

        $transaction->update([
            'access_code' => $response['access_code'] ?? null,
            'authorization_url' => $response['authorization_url'] ?? null,
        ]);

        return redirect()->away($response['authorization_url']);
    }

    public function callback(Request $request, PaystackService $paystack): RedirectResponse
    {
        $reference = (string) $request->query('reference');

        if (! $reference) {
            return redirect()->route('dashboard')->with('payment_error', 'Missing payment reference.');
        }

        $transaction = PaymentTransaction::where('reference', $reference)->firstOrFail();

        try {
            $payload = $paystack->verify($reference);
        } catch (RuntimeException $exception) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => $exception->getMessage(),
            ]);

            return redirect()->route('dashboard')->with('payment_error', 'Payment verification failed.');
        }

        if (! $this->payloadMatchesTransaction($payload, $transaction)) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => 'Payment verification mismatch.',
                'verified_payload' => $payload,
            ]);

            return redirect()->route('dashboard')->with('payment_error', 'Payment could not be verified.');
        }

        if (($payload['status'] ?? null) !== 'success') {
            $transaction->update([
                'status' => $payload['status'] ?? 'failed',
                'gateway_response' => $payload['gateway_response'] ?? 'Payment was not successful.',
                'verified_payload' => $payload,
            ]);

            return redirect()->route('dashboard')->with('payment_error', 'Payment was not completed.');
        }

        DB::transaction(function () use ($transaction, $payload) {
            $entitlement = $this->grantAccess($transaction);

            $transaction->update([
                'member_entitlement_id' => $entitlement->id,
                'status' => 'success',
                'gateway_response' => $payload['gateway_response'] ?? 'Successful',
                'paystack_transaction_id' => isset($payload['id']) ? (string) $payload['id'] : null,
                'verified_payload' => $payload,
                'paid_at' => isset($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : now(),
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('payment_success', 'Payment confirmed. Your course access has been activated.');
    }

    private function courseConfig(string $course): array
    {
        $courseConfig = config('ohc_courses.'.$course);

        abort_unless($courseConfig, 404);

        return $courseConfig;
    }

    private function grantAccess(PaymentTransaction $transaction): MemberEntitlement
    {
        $courseConfig = $this->courseConfig($transaction->course_id);
        $user = $transaction->user;

        $entitlement = MemberEntitlement::updateOrCreate(
            [
                'user_id' => $user->id,
                'external_reference' => $courseConfig['external_reference'],
            ],
            [
                'offer_name' => $courseConfig['offer_name'],
                'product_name' => $courseConfig['product_name'],
                'offer_type' => $courseConfig['offer_type'],
                'status' => 'Active',
                'started_at' => now(),
                'expires_at' => null,
            ]
        );

        if ($courseConfig['path']) {
            $user->forceFill([
                'current_path' => $this->highestPath($user->current_path, $courseConfig['path']),
            ])->save();
        }

        return $entitlement;
    }

    private function payloadMatchesTransaction(array $payload, PaymentTransaction $transaction): bool
    {
        return (string) ($payload['reference'] ?? '') === $transaction->reference
            && (int) ($payload['amount'] ?? 0) === (int) $transaction->amount
            && strtoupper((string) ($payload['currency'] ?? '')) === strtoupper($transaction->currency);
    }

    private function highestPath(?string $currentPath, string $newPath): string
    {
        $rank = [
            'Free' => 0,
            'Foundation' => 1,
            'Trader' => 2,
            'Investor' => 3,
            'Ultimate' => 4,
        ];

        return ($rank[$newPath] ?? 0) > ($rank[$currentPath] ?? 0) ? $newPath : ($currentPath ?: $newPath);
    }
}