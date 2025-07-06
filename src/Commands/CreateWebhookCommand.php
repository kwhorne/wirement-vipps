<?php

namespace Wirement\Vipps\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Wirement\Vipps\Vipps;

class CreateWebhookCommand extends Command
{
    protected $signature = 'vipps:webhook';

    protected $description = 'Create a new Vipps webhook';

    public function handle()
    {
        $this->info('Creating webhook...');

        $this->info('Fetching Vipps access token...');

        $this->info('Please enter the URL that Vipps should send the webhook to:');
        $url = $this->ask('Webhook URL');

        foreach ($this->createWebhook($url) as $key => $value) {
            $this->info($key.': '.$value);
        }
        $this->info('Please store the above values in your .env file as VIPPS_WEBHOOK_ID and VIPPS_WEBHOOK_SECRET respectively.');
    }

    private function createWebhook($url)
    {
        $vipps = new Vipps;
        $token = $vipps->getToken();
        $client = new Client;
        $response = $client->post(config('vipps.api_url').'/webhooks/v1/webhooks', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
                'Ocp-Apim-Subscription-Key' => config('vipps.subscription_key'),
                'Merchant-Serial-Number' => config('vipps.merchant_serial_number'),
                'Idempotency-Key' => Str::uuid()->toString(),
                'Vipps-System-Name' => config('vipps.system.name'),
                'Vipps-System-Version' => \Illuminate\Foundation\Application::VERSION,
                'Vipps-System-Plugin-Name' => config('vipps.system.plugin_name'),
                'Vipps-System-Plugin-Version' => config('vipps.system.plugin_version'),
            ],
            'body' => json_encode([
                'url' => $url,
                'events' => [
                    'epayments.payment.created.v1',
                    'epayments.payment.aborted.v1',
                    'epayments.payment.expired.v1',
                    'epayments.payment.cancelled.v1',
                    'epayments.payment.captured.v1',
                    'epayments.payment.refunded.v1',
                    'epayments.payment.authorized.v1',
                    'epayments.payment.terminated.v1',

                    'recurring.agreement-activated.v1',
                    'recurring.agreement-rejected.v1',
                    'recurring.agreement-stopped.v1',
                    'recurring.agreement-expired.v1',
                    'recurring.charge-reserved.v1',
                    'recurring.charge-captured.v1',
                    'recurring.charge-canceled.v1',
                    'recurring.charge-failed.v1',
                    'recurring.charge-creation-failed.v1',
                ],
            ]),
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true); // true to get associative array

        return [
            'id' => $data['id'],
            'secret' => $data['secret'],
        ];
    }
}
