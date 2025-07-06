<?php

/*
|--------------------------------------------------------------------------
| Basic Usage Examples for Wirement Vipps Package
|--------------------------------------------------------------------------
|
| This file contains examples of how to use the Wirement Vipps package
| for integrating Vipps/MobilePay payments in your Laravel application.
|
*/

use Wirement\Vipps\Facades\Vipps;
use Wirement\Vipps\Services\PaymentService;

// Example 1: Create a payment using the facade
$payment = Vipps::createPayment([
    'amount' => 10000, // Amount in øre (100 NOK)
    'currency' => 'NOK',
    'orderId' => 'order-'.time(),
    'description' => 'Test payment from Laravel',
    'redirectUrl' => 'https://your-site.com/payment/success',
    'userFlow' => 'WEB_REDIRECT',
]);

// Example 2: Create a payment using the service directly
$paymentService = new PaymentService;
$paymentLink = $paymentService->generatePaymentLink(
    10000, // Amount in øre
    'invoice-123',
    [
        [
            'id' => '1',
            'name' => 'Test Product',
            'quantity' => 1,
            'price' => 100.00,
            'vat' => 0.25, // 25% VAT
        ],
    ]
);

// Example 3: Get access token
$token = Vipps::getToken();

// Example 4: Handle payment status
$paymentStatus = Vipps::getPaymentStatus('payment-id-123');

// Example 5: Cancel a payment
$cancelResult = Vipps::cancelPayment('payment-id-123');

// Example 6: Capture a payment
$captureResult = Vipps::capturePayment('payment-id-123', 10000);

// Example 7: Refund a payment
$refundResult = Vipps::refundPayment('payment-id-123', 5000); // Partial refund of 50 NOK
