<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KajabiWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Log the request so we can see what Kajabi sends (Check storage/logs/laravel.log)
        Log::info('Kajabi Webhook Received:', $request->all());

        $data = $request->all();
        $event = $data['event'] ?? '';

        // 2. Handle "Member Created" or "Offer Purchased"
        if ($event === 'member.created' || $event === 'offer.purchased') {
            $member = $data['object'];

            $user = User::updateOrCreate(
                ['email' => $member['email']], // Find by email
                [
                    'kajabi_user_id' => $member['id'],
                    'first_name'     => $member['first_name'],
                    'last_name'      => $member['last_name'],
                    'password'       => bcrypt(str()->random(16)), // Random pass since login is via Kajabi
                    'status'         => 'Active',
                ]
            );

            // If it's an offer purchase, update their path
            if ($event === 'offer.purchased') {
                $offerTitle = $data['object']['title'];
                // Logic to map Kajabi Offer Title to your DB Paths
                $user->update(['current_path' => $this->mapOfferToPath($offerTitle)]);
            }

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'ignored']);
    }

    private function mapOfferToPath($title)
    {
        if (str_contains($title, 'Ultimate')) return 'Ultimate';
        if (str_contains($title, 'Trader')) return 'Trader';
        if (str_contains($title, 'Investor')) return 'Investor';
        if (str_contains($title, 'Foundation')) return 'Foundation';
        return $title; // Default to the product name if not a bundle
    }
}