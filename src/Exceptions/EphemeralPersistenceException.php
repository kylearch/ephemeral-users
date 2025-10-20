<?php

namespace Odie\EphemeralUsers\Exceptions;

use RuntimeException;
use Throwable;

class EphemeralPersistenceException extends RuntimeException
{
    /**
     * The ephemeral user that attempted to persist
     */
    protected mixed $ephemeralUser;

    /**
     * Create a new exception instance
     */
    public function __construct(mixed $user, ?Throwable $previous = null)
    {
        $this->ephemeralUser = $user;

        $identifier = method_exists($user, 'getEphemeralIdentifier')
            ? $user->getEphemeralIdentifier()
            : 'unknown';

        $email = $user->email ?? 'no-email';

        parent::__construct(
            "Cannot persist ephemeral user: {$email} (identifier: {$identifier})",
            0,
            $previous
        );
    }

    /**
     * Get the ephemeral user that caused this exception
     */
    public function getEphemeralUser(): mixed
    {
        return $this->ephemeralUser;
    }
}
