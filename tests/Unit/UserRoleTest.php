<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_user_is_pemilik(): void
    {
        $user = new User(['role' => 'pemilik']);
        $this->assertTrue($user->isPemilik());
    }

    public function test_user_is_not_pemilik_when_kasir(): void
    {
        $user = new User(['role' => 'kasir']);
        $this->assertFalse($user->isPemilik());
    }

    public function test_user_is_kasir(): void
    {
        $user = new User(['role' => 'kasir']);
        $this->assertTrue($user->isKasir());
    }

    public function test_user_is_not_kasir_when_pemilik(): void
    {
        $user = new User(['role' => 'pemilik']);
        $this->assertFalse($user->isKasir());
    }

    public function test_user_with_no_role_is_not_pemilik(): void
    {
        $user = new User(['role' => null]);
        $this->assertFalse($user->isPemilik());
    }

    public function test_user_with_no_role_is_not_kasir(): void
    {
        $user = new User(['role' => null]);
        $this->assertFalse($user->isKasir());
    }
}
