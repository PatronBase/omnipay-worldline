# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.3.0] - 2025-11-14

### Added

- Support for hosted purchases being in 'pending' state (notably for 3DS state webhook callbacks)
- Changelog!

## [3.2.0] - 2025-08-08

### Added

- Saved card support

## [3.1.0] - 2025-07-28

### Added

- `refund() support, including pending refunds
- `fetchTransaction()` support
- support for MOTO transactions

### Changed

- Complete purchase requests validate necessary URL parameters
- Use a common, local `AbstractRequest`

## [3.0.1] - 2025-05-02

### Changed

- Explicitly mark as e-commerce transactions
- Request method changed to class property

### Fixed

- Purchase should include authorisation and capture

## [3.0.0] - 2025-05-01

Initial version

[3.3.0]: https://github.com/Patronbase/omnipay-worldline/compare/v3.2.0...v3.3.0
[3.2.0]: https://github.com/Patronbase/omnipay-worldline/compare/v3.1.0...v3.2.0
[3.1.0]: https://github.com/Patronbase/omnipay-worldline/compare/v3.0.1...v3.1.0
[3.0.1]: https://github.com/Patronbase/omnipay-worldline/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/Patronbase/omnipay-worldline/tree/v3.0.0
