<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdox 作成可能なケース
     * @testWith ["foo", "test@example.com", "qwerty"]
     */
    public function tes_can_create(string $name, string $email, string $password): void
    {
        $user = User::create(compact('name', 'email', 'password'));
        $this->assertNotNull($user);
    }

    /**
     * @testdox 必要なカラムが不足しているケース
     * @testWith [null, "test@example.com", "qwerty"]
     *           ["foo", null, "qwerty"]
     *           ["foo", "test@example.com", null]
     */
    public function test_cannot_create(?string $name, ?string $email, ?string $password): void
    {
        $this->assertThrows(function () use ($name, $email, $password) {
            User::create(compact('name', 'email', 'password'));
        });
    }
}
