# Release Checklist for Packagist

## Pre-Release Checklist

### ✅ Code Quality
- [x] All PHPStan errors resolved
- [x] Code formatted with Laravel Pint
- [x] All tests passing
- [x] Composer.json validated

### ✅ Documentation
- [x] README.md updated with examples
- [x] CHANGELOG.md created with version 1.0.0
- [x] CONTRIBUTING.md created
- [x] LICENSE.md updated with correct copyright
- [x] Examples created for Livewire and Filament

### ✅ Package Structure
- [x] Correct namespace: `Wirement\Vipps`
- [x] Service provider: `VippsServiceProvider`
- [x] Facade: `Vipps`
- [x] Configuration file: `config/vipps.php`
- [x] Migration: `create_vipps_tokens_table.php.stub`
- [x] Commands: `CreateWebhookCommand`
- [x] Models: `VippsToken`
- [x] Services: `PaymentService`

### ✅ Composer Dependencies
- [x] PHP ^8.1
- [x] Laravel ^10.0||^11.0||^12.0
- [x] GuzzleHttp ^7.0
- [x] Carbon ^2.0||^3.0
- [x] Spatie Laravel Package Tools ^1.16

## Release Steps for Packagist

1. **Commit all changes to GitHub**
   ```bash
   git add .
   git commit -m "Release 1.0.0: Initial release of Wirement Vipps package"
   git push origin main
   ```

2. **Create a Git tag**
   ```bash
   git tag -a v1.0.0 -m "Version 1.0.0: Initial release"
   git push origin v1.0.0
   ```

3. **Submit to Packagist**
   - Go to https://packagist.org/packages/submit
   - Enter repository URL: https://github.com/kwhorne/wirement-vipps
   - Click "Check"

4. **Verify installation works**
   ```bash
   composer require kwhorne/wirement-vipps
   ```

## Post-Release Verification

- [ ] Package available on Packagist
- [ ] Installation works via composer
- [ ] Service provider auto-discovery works
- [ ] Config publishing works
- [ ] Migration publishing works
- [ ] Command registration works

## Version History

- **v1.0.0**: Initial release with full Vipps/MobilePay integration
