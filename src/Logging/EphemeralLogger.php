<?php

namespace Odie\EphemeralUsers\Logging;

use Illuminate\Support\Facades\Log;

class EphemeralLogger
{
    /**
     * Log an ephemeral persistence attempt
     */
    public function log(mixed $user, array $context = []): void
    {
        $channel = config('ephemeral-users.log_channel', 'stack');
        $level = config('ephemeral-users.log_level', 'warning');

        $message = 'Ephemeral user persist attempt - consider refactoring';

        $logContext = array_merge([
            'package' => 'ephemeral-users',
            'user_class' => get_class($user),
        ], $context);

        // Use the configured log level
        Log::channel($channel)->{$level}($message, $logContext);
    }

    /**
     * Log with custom "upgrade" level if configured
     * This is a convenience method for the custom UPGRADE log level
     */
    public function upgrade(string $message, array $context = []): void
    {
        $channel = config('ephemeral-users.log_channel', 'stack');

        Log::channel($channel)->info('[UPGRADE] '.$message, array_merge([
            'log_level' => 'upgrade',
        ], $context));
    }
}
