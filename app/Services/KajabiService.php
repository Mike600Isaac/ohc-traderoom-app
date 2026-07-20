<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KajabiService
{
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->apiKey = config('services.kajabi.key');
        $this->apiSecret = config('services.kajabi.secret');
    }

    public function getKajabiMember($email)
    {
        // Kajabi API v1 requires these as query parameters
        $response = Http::get("https://kajabi.com/api/v1/members", [
            'api_key'    => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'email'      => $email
        ]);

        if ($response->successful() && isset($response->json()['members'][0])) {
            return $response->json()['members'][0];
        }

        return null;
    }
}