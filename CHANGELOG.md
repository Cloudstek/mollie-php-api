# Change log

## [2.0.0]
### Added
- This beautiful change log
- Method for mandate revocation
- Method for updating customer details

### Changed
- Allow metadata to be entered as array or object instead of just array
- Send post request as JSON instead of form encoded
- No longer send metadata as JSON string as the whole request is sent as JSON now
- **Removed** methodParams option in PaymentResource $opts parameter. Add method parameters to $opts directly.
- **Removed** methodParams option in Customer\\PaymentResource $opts parameter. Add method parameters to $opts directly.
- **Removed** method and webhookUrl parameters from Customer\\SubscriptionResource. Add them to new parameter $opts instead for consistency.
- **Renamed** $opts parameters bic to consumerBic and reference to mandateReference in Customer\\MandateResource

### Fixed
- Clean up code and fix most of the open CodeClimate issues

## [1.0.2] - 2016-10-20
### Fixed
- Possible class name conflict in different namespaces

## [1.0.1] - 2016-09-20
### Fixed
- Wrong javascript style append assignment operator which crashed PHP 7 builds
- Trailing "T" in expiryPeriod value returned by Mollie API in test mode, which returned a non-valid ISO 8601 string.

## 1.0.0 - 2016-08-23
Initial release

[1.1.0]: https://github.com/Cloudstek/mollie-php-api/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/Cloudstek/mollie-php-api/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/Cloudstek/mollie-php-api/compare/v1.0.0...v1.0.1
