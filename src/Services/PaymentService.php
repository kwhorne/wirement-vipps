<?php

namespace Wirement\Vipps\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Wirement\Vipps\Vipps;

class PaymentService
{

    // Orderlines array:
    // [
    //     [
    //         'id' => '1',
    //         'name' => 'Product name',
    //         'quantity' => 1,
    //         'price' => 100,
    //         'vat' => 25,
    //     ],

    private function handleOrderLines($orderlines)
    {

        $lines = [];
        foreach($orderlines as $line) {
            $lines[] = [
                'name' => $line['name'],
                'id' => $line['id'],
                'totalAmount' => (int) ($line['price']*100),
                'totalAmountExcludingTax' => (int) (round($line['price'] / (1 + $line['vat']), 2)*100),
                'totalTaxAmount' => (int) (round($line['price'] - ($line['price'] / (1 + $line['vat'])), 2)*100),
                'taxPercentage' => (int) ($line['vat'] * 100),
                'unitInfo' => [
                    'unitPrice' => (int) ($line['price']*100),
                    'quantity' => $line['quantity'],
                ],
            ];
        }
        return $lines;
    }


    public function generatePaymentLink($amount, $invoiceNumber, $orderlines = null)
    {
        $body = [
            'amount' => [
                'currency' => env('VIPPS_CURRENCY'),
                'value' => $amount,
            ],
            'paymentMethod' => [
                'type' => 'WALLET',
            ],
            'customerInteraction' => 'CUSTOMER_NOT_PRESENT',
            'reference' => 'vipps-'.Str::uuid()->toString(),
            'userFlow' => 'WEB_REDIRECT',
            'returnUrl' => env('VIPPS_RETURN_URL'),
            'recipt' =>[
                'orderLines' => [],
                'bottomLine' => [
                    'currency' => env('VIPPS_CURRENCY'),
                    'posId' => $invoiceNumber,
                    'reciptNumber' => $invoiceNumber,
                ],
            ]
        ];
        if($orderlines) {
            $body['recipt']['orderLines'] = $this->handleOrderLines($orderlines);
        }
        $vipps = new Vipps();
        $token = $vipps->getToken();

        $client = new Client();

        $response = $client->post(env('VIPPS_API_URL').'/epayment/v1/payments', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
                'Ocp-Apim-Subscription-Key' => env('VIPPS_SUBSCRIPTION_KEY'),
                'Merchant-Serial-Number' => env('VIPPS_MERCHANT_SERIAL_NUMBER'),
                'Idempotency-Key' => Str::uuid()->toString(),
                'Vipps-System-Name' => env('APP_NAME'),
                'Vipps-System-Version' => \Illuminate\Foundation\Application::VERSION,
                'Vipps-System-Plugin-Name' => 'Vipps-Laravel',
                'Vipps-System-Plugin-Version' => '1.0.0',
            ],
            'body' => json_encode($body),
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true); // true to get associative array

        return $data['redirectUrl'];
    }
}