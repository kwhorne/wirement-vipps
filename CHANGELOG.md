# Changelog

All notable changes to `wirement-vipps` will be documented in this file.

## 1.0.0 - 2025-07-06

### Added
- Initial release of Wirement Vipps package
- Vipps/MobilePay payment integration for Laravel
- Support for Laravel 10, 11, and 12
- Token management with automatic expiration handling
- Payment creation and management
- Webhook command for easy setup
- Facade for easy access (`Vipps::` syntax)
- Livewire component example
- Filament resource example
- Comprehensive configuration file
- Database migration for token storage
- Full documentation and examples

### Features
- **Payment Creation**: Create Vipps payments with ease
- **Token Management**: Automatic token refresh and caching
- **Webhook Support**: Easy webhook setup with Artisan command
- **Laravel Integration**: Full Laravel ecosystem support
- **Flux UI Compatible**: Designed for Laravel Flux projects
- **Livewire Ready**: Includes Livewire component examples
- **Filament Integration**: Ready-to-use Filament resources
- **Modern Architecture**: Clean, service-oriented design
- **Configuration**: Comprehensive config file with all options
- **Migration**: Database migration for token storage

### Dependencies
- PHP ^8.1
- Laravel ^10.0||^11.0||^12.0
- GuzzleHttp ^7.0
- Carbon ^2.0||^3.0
- Spatie Laravel Package Tools ^1.16
