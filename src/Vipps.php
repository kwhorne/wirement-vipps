<?php

namespace Wirement\Vipps;

use GuzzleHttp\Client;
use Wirement\Vipps\Models\VippsToken;
use Illuminate\Foundation\Application;
use Carbon\Carbon;

class Vipps
{
    public function getToken()
    {
        // Use Carbon for better date handling
        $now = Carbon::now();
        // get the token from the database if it exists and is not expired
        if(VippsToken::where('expires_at', '>', $now)->exists()){
            $token = VippsToken::where('expires_at', '>', $now)->first();
            return $token->token;
        }

        // send a request to the mobilepay api to get the access token with headers
        $client = new Client();

        $response = $client->post(config('vipps.api_url').'/accesstoken/get', [
            'headers' => [
                'Content-Type' => 'application/json',
                'client_id' => config('vipps.client_id'),
                'client_secret' => config('vipps.client_secret'),
                'Ocp-Apim-Subscription-Key' => config('vipps.subscription_key'),
                'Merchant-Serial-Number' => config('vipps.merchant_serial_number'),
                'Vipps-System-Name' => config('app.name'),
                'Vipps-System-Version' => Application::VERSION,
                'Vipps-System-Plugin-Name' => 'Wirement-Vipps',
                'Vipps-System-Plugin-Version' => '1.0.0',
            ]
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true); // true to get associative array

        $expiresAt = $now->addSeconds($data['expires_in']);

        VippsToken::create([
            'token' => $data['access_token'],
            'expires_at' => $expiresAt,
        ]);

        return $data['access_token'];
    }
}