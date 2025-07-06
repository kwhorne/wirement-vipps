# Wirement Vipps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kwhorne/wirement-vipps.svg?style=flat-square)](https://packagist.org/packages/kwhorne/wirement-vipps)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kwhorne/wirement-vipps/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kwhorne/wirement-vipps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kwhorne/wirement-vipps/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kwhorne/wirement-vipps/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kwhorne/wirement-vipps.svg?style=flat-square)](https://packagist.org/packages/kwhorne/wirement-vipps)

A Laravel package for Vipps/MobilePay payment integration designed for Laravel applications using Flux UI, Livewire, TailwindCSS, and Filament. This package provides a seamless integration with Vipps and MobilePay payment services.

# VippsForLaravel

This package is for Laravel to accept Vipps/Mobilepay payments. Plug and play, you just need to register your business at MobilePay.dk/Vipps.no

# Usage
Put the following variables in your .env
```
VIPPS_CLIENT_ID=
VIPPS_CLIENT_SECRET=
VIPPS_MERCHANT_SERIAL_NUMBER=
VIPPS_SUBSCRIPTION_KEY=
VIPPS_CURRENCY="DKK"
VIPPS_API_URL=https://apitest.vipps.no #For testing
VIPPS_API_URL=https://api.vipps.no #For production
VIPPS_RETURN_URL=
VIPPS_WEBHOOK_ID=
VIPPS_WEBHOOK_SECRET=
```
To produce a webhook, just use the command `php artisan vipps:webhook`

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
use Wirement\Vipps\Facades\Vipps;

// Create a payment using the facade
$payment = Vipps::createPayment([
    'amount' => 10000, // Amount in øre (100 NOK)
    'currency' => 'NOK',
    'orderId' => 'order-123',
    'description' => 'Test payment',
    'redirectUrl' => 'https://your-site.com/payment/callback',
    'userFlow' => 'WEB_REDIRECT'
]);

// Get the payment URL
$paymentUrl = $payment['url'];
```

### Using with Livewire

```php
use Livewire\Component;
use Wirement\Vipps\Facades\Vipps;

class PaymentComponent extends Component
{
    public function createPayment()
    {
        $payment = Vipps::createPayment([
            'amount' => 5000, // 50 NOK
            'currency' => 'NOK',
            'orderId' => 'order-' . time(),
            'description' => 'Product purchase',
            'redirectUrl' => route('payment.callback'),
        ]);
        
        return redirect()->to($payment['url']);
    }
}
```

### Using with Filament

```php
use Filament\Resources\Resource;
use Wirement\Vipps\Facades\Vipps;

// In your Filament resource
Tables\Actions\Action::make('create_payment')
    ->label('Create Vipps Payment')
    ->action(function ($record) {
        $payment = Vipps::createPayment([
            'amount' => $record->total * 100, // Convert to øre
            'currency' => 'NOK',
            'orderId' => $record->id,
            'description' => 'Order #' . $record->id,
            'redirectUrl' => route('orders.callback', $record),
        ]);
        
        $record->update(['vipps_url' => $payment['url']]);
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
