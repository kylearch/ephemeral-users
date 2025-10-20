# Changelog

All notable changes to `ephemeral-users` will be documented in this file.

## [Unreleased]

## [1.0.0] - 2025-01-XX

### Added
- Initial release
- `HasEphemeralState` trait for adding ephemeral functionality to models
- `EphemeralUser` interface for type safety
- `EphemeralPersistenceException` for handling save attempts
- Configurable logging of persistence attempts
- Configurable exception throwing behavior
- Automatic call stack tracking in logs
- Service provider with singleton logger
- Comprehensive test suite (26 tests, 49 assertions)
- Full documentation and usage examples

### Features
- Create ephemeral user instances with `User::ephemeral()`
- Check ephemeral state with `isEphemeral()`
- Get unique identifiers with `getEphemeralIdentifier()`
- Prevent accidental database persistence
- Track code paths attempting to persist ephemeral users
- Configure behavior via published config file

[Unreleased]: https://github.com/kylearch/ephemeral-users/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/kylearch/ephemeral-users/releases/tag/v1.0.0
