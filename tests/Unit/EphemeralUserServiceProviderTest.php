<?php

namespace Odie\EphemeralUsers\Tests\Unit;

use Odie\EphemeralUsers\Logging\EphemeralLogger;
use Odie\EphemeralUsers\Tests\TestCase;

class EphemeralUserServiceProviderTest extends TestCase
{
    public function test_service_provider_registers_logger(): void
    {
        $logger = $this->app->make('ephemeral.logger');

        $this->assertInstanceOf(EphemeralLogger::class, $logger);
    }

    public function test_logger_is_singleton(): void
    {
        $logger1 = $this->app->make('ephemeral.logger');
        $logger2 = $this->app->make('ephemeral.logger');

        $this->assertSame($logger1, $logger2);
    }

    public function test_config_is_loaded(): void
    {
        $this->assertTrue(config()->has('ephemeral-users.throw_on_persist'));
        $this->assertTrue(config()->has('ephemeral-users.log_attempts'));
        $this->assertTrue(config()->has('ephemeral-users.log_channel'));
        $this->assertTrue(config()->has('ephemeral-users.log_level'));
    }

    public function test_config_has_correct_defaults(): void
    {
        $this->assertTrue(config('ephemeral-users.throw_on_persist'));
        $this->assertTrue(config('ephemeral-users.log_attempts'));
        $this->assertEquals('stack', config('ephemeral-users.log_channel'));
        $this->assertEquals('warning', config('ephemeral-users.log_level'));
    }
}
