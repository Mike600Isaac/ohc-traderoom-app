<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function initialize(array $payload): array
    {
        $response = $this->client()->post('/transaction/initialize', $payload);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException($response->json('message') ?: 'Unable to initialize payment.');
        }

        return $response->json('data');
    }

    public function verify(string $reference): array
    {
        $response = $this->client()->get('/transaction/verify/'.$reference);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException($response->json('message') ?: 'Unable to verify payment.');
        }

        return $response->json('data');
    }

    private function client(): PendingRequest
    {
        $secretKey = config('services.paystack.secret_key');

        if (! $secretKey) {
            throw new RuntimeException('Payment is not configured yet. Please add the Paystack secret key.');
        }

        return Http::baseUrl('https://api.paystack.co')
            ->acceptJson()
            ->asJson()
            ->withToken($secretKey)
            ->timeout(20);
    }
}
