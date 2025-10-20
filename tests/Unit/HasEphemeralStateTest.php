<?php

namespace Odie\EphemeralUsers\Tests\Unit;

use Illuminate\Support\Facades\Log;
use Odie\EphemeralUsers\Exceptions\EphemeralPersistenceException;
use Odie\EphemeralUsers\Tests\TestCase;
use Odie\EphemeralUsers\Tests\TestUser;

class HasEphemeralStateTest extends TestCase
{
    public function test_can_create_ephemeral_user(): void
    {
        $user = TestUser::ephemeral([
            'id' => 'test-123',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertTrue($user->isEphemeral());
        $this->assertFalse($user->exists);
        $this->assertEquals('test-123', $user->id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_ephemeral_user_sets_attributes_even_if_not_fillable(): void
    {
        $user = TestUser::ephemeral([
            'id' => 'test-123',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'custom_field' => 'custom_value',
        ]);

        $this->assertEquals('custom_value', $user->custom_field);
    }

    public function test_regular_user_is_not_ephemeral(): void
    {
        $user = new TestUser([
            'email' => 'regular@example.com',
            'name' => 'Regular User',
        ]);

        $this->assertFalse($user->isEphemeral());
    }

    public function test_get_ephemeral_identifier_returns_ephemeral_id(): void
    {
        $user = TestUser::ephemeral([
            'ephemeral_id' => 'session-abc123',
            'id' => 'db-id',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('session-abc123', $user->getEphemeralIdentifier());
    }

    public function test_get_ephemeral_identifier_falls_back_to_id(): void
    {
        $user = TestUser::ephemeral([
            'id' => 'test-id',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test-id', $user->getEphemeralIdentifier());
    }

    public function test_get_ephemeral_identifier_falls_back_to_email(): void
    {
        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test@example.com', $user->getEphemeralIdentifier());
    }

    public function test_get_ephemeral_identifier_returns_unknown_when_no_attributes(): void
    {
        $user = TestUser::ephemeral([]);

        $this->assertEquals('unknown', $user->getEphemeralIdentifier());
    }

    public function test_ephemeral_user_throws_exception_on_save_when_configured(): void
    {
        config(['ephemeral-users.throw_on_persist' => true]);

        $user = TestUser::ephemeral([
            'id' => 'test-123',
            'email' => 'test@example.com',
        ]);

        $this->expectException(EphemeralPersistenceException::class);
        $this->expectExceptionMessage('Cannot persist ephemeral user: test@example.com');

        $user->save();
    }

    public function test_ephemeral_user_prevents_save_without_exception_when_configured(): void
    {
        config(['ephemeral-users.throw_on_persist' => false]);

        $user = TestUser::ephemeral([
            'id' => 'test-123',
            'email' => 'test@example.com',
        ]);

        $result = $user->save();

        $this->assertFalse($result);
        $this->assertFalse($user->exists);
    }

    public function test_ephemeral_user_logs_persist_attempt_when_configured(): void
    {
        config(['ephemeral-users.log_attempts' => true]);
        config(['ephemeral-users.throw_on_persist' => false]);

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Ephemeral user persist attempt - consider refactoring'
                    && $context['package'] === 'ephemeral-users'
                    && $context['identifier'] === 'test@example.com';
            });

        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $user->save();
    }

    public function test_ephemeral_user_does_not_log_when_disabled(): void
    {
        config(['ephemeral-users.log_attempts' => false]);
        config(['ephemeral-users.throw_on_persist' => false]);

        Log::shouldReceive('channel')->never();

        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $user->save();
    }

    public function test_ephemeral_user_uses_custom_log_channel(): void
    {
        config(['ephemeral-users.log_attempts' => true]);
        config(['ephemeral-users.log_channel' => 'custom']);
        config(['ephemeral-users.throw_on_persist' => false]);

        Log::shouldReceive('channel')
            ->once()
            ->with('custom')
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once();

        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $user->save();
    }

    public function test_ephemeral_user_uses_custom_log_level(): void
    {
        config(['ephemeral-users.log_attempts' => true]);
        config(['ephemeral-users.log_level' => 'error']);
        config(['ephemeral-users.throw_on_persist' => false]);

        Log::shouldReceive('channel')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('error')
            ->once();

        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $user->save();
    }

    public function test_call_stack_summary_is_included_in_log_context(): void
    {
        config(['ephemeral-users.log_attempts' => true]);
        config(['ephemeral-users.throw_on_persist' => false]);

        Log::shouldReceive('channel')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return isset($context['trace']) && is_array($context['trace']);
            });

        $user = TestUser::ephemeral([
            'email' => 'test@example.com',
        ]);

        $user->save();
    }
}
