<?php

namespace Odie\EphemeralUsers\Contracts;

interface EphemeralUser
{
    /**
     * Create an ephemeral instance of the user model
     */
    public static function ephemeral(array $attributes): static;

    /**
     * Check if this user instance is ephemeral
     */
    public function isEphemeral(): bool;

    /**
     * Get the unique identifier for this ephemeral user
     */
    public function getEphemeralIdentifier(): string;
}
