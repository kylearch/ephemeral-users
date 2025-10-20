<?php

namespace Odie\EphemeralUsers\Tests\Unit;

use Illuminate\Support\Facades\Log;
use Odie\EphemeralUsers\Logging\EphemeralLogger;
use Odie\EphemeralUsers\Tests\TestCase;
use Odie\EphemeralUsers\Tests\TestUser;

class EphemeralLoggerTest extends TestCase
{
    public function test_log_method_uses_configured_channel_and_level(): void
    {
        config(['ephemeral-users.log_channel' => 'test-channel']);
        config(['ephemeral-users.log_level' => 'info']);

        $user = TestUser::ephemeral(['email' => 'test@example.com']);

        Log::shouldReceive('channel')
            ->once()
            ->with('test-channel')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Ephemeral user persist attempt - consider refactoring'
                    && $context['package'] === 'ephemeral-users'
                    && isset($context['user_class']);
            });

        $logger = new EphemeralLogger;
        $logger->log($user);
    }

    public function test_log_method_includes_custom_context(): void
    {
        config(['ephemeral-users.log_channel' => 'stack']);
        config(['ephemeral-users.log_level' => 'warning']);

        $user = TestUser::ephemeral(['email' => 'test@example.com']);

        Log::shouldReceive('channel')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['custom_key'] === 'custom_value';
            });

        $logger = new EphemeralLogger;
        $logger->log($user, ['custom_key' => 'custom_value']);
    }

    public function test_upgrade_method_logs_with_upgrade_prefix(): void
    {
        config(['ephemeral-users.log_channel' => 'stack']);

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_starts_with($message, '[UPGRADE]')
                    && $context['log_level'] === 'upgrade';
            });

        $logger = new EphemeralLogger;
        $logger->upgrade('Test upgrade message');
    }

    public function test_log_method_uses_default_values_when_config_missing(): void
    {
        // Set config values to default behavior by recreating the package config
        config(['ephemeral-users' => []]);

        $user = TestUser::ephemeral(['email' => 'test@example.com']);

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once();

        $logger = new EphemeralLogger;
        $logger->log($user);
    }
}
