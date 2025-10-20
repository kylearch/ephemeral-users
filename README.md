# Ephemeral Users for Laravel

A Laravel package for creating ephemeral (non-persistent) user instances. Perfect for handling anonymous users, session-based users, or any scenario where you need a User object without database persistence.

## Features

- 🚫 **Prevent Accidental Persistence** - Ephemeral users throw exceptions when save attempts are made
- 📝 **Automatic Logging** - Track code paths that attempt to persist ephemeral users
- 🔧 **Configurable Behavior** - Choose between throwing exceptions or silently preventing saves
- 🎯 **Type Safe** - Full type hinting and interface support
- 📦 **Easy Integration** - Simple trait-based implementation

## Installation

Add the package to your Laravel application:

```bash
composer require odie/ephemeral-users
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=ephemeral-users-config
```

## Usage

### Basic Setup

Implement the interface and use the trait in your User model:

```php
use Odie\EphemeralUsers\Contracts\EphemeralUser as EphemeralUserContract;
use Odie\EphemeralUsers\Concerns\HasEphemeralState;

class User extends Authenticatable implements EphemeralUserContract
{
    use HasEphemeralState;

    // Your existing User model code...
}
```

### Creating Ephemeral Users

```php
// Create an ephemeral user instance
$ephemeralUser = User::ephemeral([
    'id' => 'session-abc123',
    'email' => 'anonymous@example.com',
    'name' => 'Anonymous User',
]);

// Check if a user is ephemeral
if ($ephemeralUser->isEphemeral()) {
    // Handle ephemeral user logic
}

// Get the ephemeral identifier
$identifier = $ephemeralUser->getEphemeralIdentifier(); // 'session-abc123'
```

### Persistence Protection

Attempting to save an ephemeral user will:
1. Log the attempt (if logging is enabled)
2. Throw an `EphemeralPersistenceException` (if configured)
3. Prevent the save operation

```php
$ephemeralUser = User::ephemeral(['id' => 'test']);

try {
    $ephemeralUser->save(); // Throws EphemeralPersistenceException
} catch (EphemeralPersistenceException $e) {
    // Handle the exception
    $user = $e->getEphemeralUser();
}
```

## Configuration

The package supports the following configuration options:

```php
return [
    // Throw exception on persist attempts (default: true)
    'throw_on_persist' => env('EPHEMERAL_THROW_ON_PERSIST', true),

    // Log persist attempts (default: true)
    'log_attempts' => env('EPHEMERAL_LOG_PERSIST', true),

    // Log channel to use
    'log_channel' => env('EPHEMERAL_LOG_CHANNEL', 'stack'),

    // Log level for persist attempts
    'log_level' => env('EPHEMERAL_LOG_LEVEL', 'warning'),
];
```

## Use Cases

- **Anonymous Users**: Handle session-based users without database records
- **Testing**: Create test users without database persistence
- **API Integration**: Represent external users without local persistence
- **Guest Checkout**: Allow guest users with full User object interface
- **Sample/Demo Flows**: Enable trial experiences without account creation

## License

MIT
