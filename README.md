# Wirement Vipps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kwhorne/wirement-vipps.svg?style=flat-square)](https://packagist.org/packages/kwhorne/wirement-vipps)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kwhorne/wirement-vipps/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kwhorne/wirement-vipps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kwhorne/wirement-vipps/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kwhorne/wirement-vipps/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kwhorne/wirement-vipps.svg?style=flat-square)](https://packagist.org/packages/kwhorne/wirement-vipps)

A Laravel package for Vipps/MobilePay payment integration designed for Laravel applications using Flux UI, Livewire, TailwindCSS, and Filament. This package provides a seamless integration with Vipps and MobilePay payment services.

## Features

- 🚀 **Easy Setup**: Simple configuration and installation
- 💳 **Payment Creation**: Create Vipps payments with ease
- 🔐 **Token Management**: Automatic token refresh and caching
- 📡 **Webhook Support**: Easy webhook setup with Artisan command
- 🏗️ **Laravel Integration**: Full Laravel ecosystem support
- ⚡ **Flux UI Compatible**: Designed for Laravel Flux projects
- 🔴 **Livewire Ready**: Includes Livewire component examples
- 📊 **Filament Integration**: Ready-to-use Filament resources
- 🏛️ **Modern Architecture**: Clean, service-oriented design
- ⚙️ **Comprehensive Config**: All options in one config file

## Requirements

- PHP ^8.1
- Laravel ^10.0||^11.0||^12.0
- GuzzleHttp ^7.0
- Carbon ^2.0||^3.0

## Configuration

Add the following variables to your `.env` file:

```env
VIPPS_CLIENT_ID=your_client_id
VIPPS_CLIENT_SECRET=your_client_secret
VIPPS_MERCHANT_SERIAL_NUMBER=your_merchant_serial_number
VIPPS_SUBSCRIPTION_KEY=your_subscription_key
VIPPS_CURRENCY=NOK
VIPPS_API_URL=https://apitest.vipps.no # For testing
# VIPPS_API_URL=https://api.vipps.no # For production
VIPPS_RETURN_URL=https://your-site.com/payment/callback
VIPPS_WEBHOOK_ID=your_webhook_id
VIPPS_WEBHOOK_SECRET=your_webhook_secret
```

### Create Webhook

To create a webhook, use the Artisan command:

```bash
php artisan vipps:webhook
```

This will guide you through the webhook creation process and provide you with the webhook ID and secret to add to your `.env` file.

## Installation

You can install the package via composer:

```bash
composer require kwhorne/wirement-vipps
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="wirement-vipps-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="wirement-vipps-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="wirement-vipps-views"
```

## Usage

### Basic Payment Creation

```php
use Wirement\Vipps\Services\PaymentService;

// Create a payment using the service
$paymentService = new PaymentService();
$paymentUrl = $paymentService->generatePaymentLink(
    10000, // Amount in øre (100 NOK)
    'order-123' // Invoice/Order number
);

// Redirect user to payment URL
return redirect()->to($paymentUrl);
```

### Payment with Order Lines

```php
use Wirement\Vipps\Services\PaymentService;

$orderlines = [
    [
        'id' => '1',
        'name' => 'Product name',
        'quantity' => 1,
        'price' => 100, // Price in NOK
        'vat' => 0.25, // 25% VAT
    ],
];

$paymentService = new PaymentService();
$paymentUrl = $paymentService->generatePaymentLink(
    10000, // Amount in øre
    'order-123', // Invoice number
    $orderlines // Optional order lines
);
```

### Using with Livewire

```php
use Livewire\Component;
use Wirement\Vipps\Services\PaymentService;

class PaymentComponent extends Component
{
    public function createPayment()
    {
        $paymentService = new PaymentService();
        $paymentUrl = $paymentService->generatePaymentLink(
            5000, // 50 NOK in øre
            'order-' . time() // Invoice number
        );
        
        return redirect()->to($paymentUrl);
    }
}
```

### Using with Filament

```php
use Filament\Resources\Resource;
use Wirement\Vipps\Services\PaymentService;

// In your Filament resource
Tables\Actions\Action::make('create_payment')
    ->label('Create Vipps Payment')
    ->action(function ($record) {
        $paymentService = new PaymentService();
        $paymentUrl = $paymentService->generatePaymentLink(
            $record->total * 100, // Convert to øre
            (string) $record->id // Invoice number
        );
        
        $record->update(['vipps_url' => $paymentUrl]);
    })
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Knut Horne](https://github.com/kwhorne)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
