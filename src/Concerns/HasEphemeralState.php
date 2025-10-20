<?php

namespace KyleArch\EphemeralUsers\Concerns;

use KyleArch\EphemeralUsers\Exceptions\EphemeralPersistenceException;

trait HasEphemeralState
{
    /**
     * Indicates if this instance is ephemeral (non-persistent)
     */
    protected bool $ephemeral = false;

    /**
     * Create an ephemeral instance that cannot be persisted
     */
    public static function ephemeral(array $attributes): static
    {
        $instance = new static;

        // Forcefully set attributes even if not fillable (ephemeral instances don't persist)
        foreach ($attributes as $key => $value) {
            $instance->setAttribute($key, $value);
        }

        $instance->ephemeral = true;
        $instance->exists = false;

        return $instance;
    }

    /**
     * Check if this instance is ephemeral
     */
    public function isEphemeral(): bool
    {
        return $this->ephemeral;
    }

    /**
     * Get the ephemeral identifier for this user
     */
    public function getEphemeralIdentifier(): string
    {
        // Check custom ephemeral_id attribute first (for ephemeral instances)
        // Then fall back to id or email for authenticated users
        return $this->getAttribute('ephemeral_id')
            ?? $this->id
            ?? $this->email
            ?? 'unknown';
    }

    /**
     * Boot the HasEphemeralState trait
     */
    protected static function bootHasEphemeralState(): void
    {
        // Intercept save attempts on ephemeral instances
        static::saving(function ($model) {
            if ($model->isEphemeral()) {
                return $model->handleEphemeralPersistAttempt();
            }
        });
    }

    /**
     * Handle an attempt to persist an ephemeral instance
     *
     * @return bool Returns false to prevent the save operation
     *
     * @throws EphemeralPersistenceException
     */
    protected function handleEphemeralPersistAttempt(): bool
    {
        // Always log the attempt
        if (config('ephemeral-users.log_attempts', true)) {
            $this->logEphemeralPersistAttempt();
        }

        // Throw exception if configured to do so
        if (config('ephemeral-users.throw_on_persist', true)) {
            throw new EphemeralPersistenceException($this);
        }

        // Cancel the save operation
        return false;
    }

    /**
     * Log an ephemeral persist attempt for tracking
     */
    protected function logEphemeralPersistAttempt(): void
    {
        $logger = app('ephemeral.logger');

        $logger->log($this, [
            'identifier' => $this->getEphemeralIdentifier(),
            'email' => $this->email ?? null,
            'trace' => $this->getCallStackSummary(),
        ]);
    }

    /**
     * Get a summary of the call stack for debugging
     */
    protected function getCallStackSummary(): array
    {
        return collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
            ->map(fn ($trace) => ($trace['class'] ?? '')
                .($trace['type'] ?? '')
                .($trace['function'] ?? ''))
            ->filter()
            ->take(5)
            ->values()
            ->all();
    }
}
