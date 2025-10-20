<?php

namespace KyleArch\EphemeralUsers\Tests\Unit;

use KyleArch\EphemeralUsers\Exceptions\EphemeralPersistenceException;
use KyleArch\EphemeralUsers\Tests\TestCase;
use KyleArch\EphemeralUsers\Tests\TestUser;

class EphemeralPersistenceExceptionTest extends TestCase
{
    public function test_exception_contains_user_instance(): void
    {
        $user = TestUser::ephemeral([
            'id' => 'test-123',
            'email' => 'test@example.com',
        ]);

        $exception = new EphemeralPersistenceException($user);

        $this->assertSame($user, $exception->getEphemeralUser());
    }

    public function test_exception_message_includes_email_and_identifier(): void
    {
        $user = TestUser::ephemeral([
            'ephemeral_id' => 'session-abc',
            'email' => 'test@example.com',
        ]);

        $exception = new EphemeralPersistenceException($user);

        $this->assertStringContainsString('test@example.com', $exception->getMessage());
        $this->assertStringContainsString('session-abc', $exception->getMessage());
    }

    public function test_exception_message_handles_missing_email(): void
    {
        $user = TestUser::ephemeral([
            'ephemeral_id' => 'session-abc',
        ]);

        $exception = new EphemeralPersistenceException($user);

        $this->assertStringContainsString('no-email', $exception->getMessage());
        $this->assertStringContainsString('session-abc', $exception->getMessage());
    }

    public function test_exception_message_handles_missing_identifier_method(): void
    {
        $user = new class
        {
            public string $email = 'test@example.com';
        };

        $exception = new EphemeralPersistenceException($user);

        $this->assertStringContainsString('unknown', $exception->getMessage());
    }
}
