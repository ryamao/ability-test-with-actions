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
     * @group model
     * @testWith ["foo", "test@example.com", "qwerty"]
     */
    public function tes_can_create(string $name, string $email, string $password): void
    {
        $user = User::create(compact('name', 'email', 'password'));
        $this->assertNotNull($user);
    }

    /**
     * @testdox 必要なカラムが不足しているケース
     * @group model
     * @testWith [null, "test@example.com", "qwerty"]
     *           ["foo", null, "qwerty"]
     *           ["foo", "test@example.com", null]
     */
    public function test_cannot_create(?string $name, ?string $email, ?string $password): void
    {
        $params = compact('name', 'email', 'password');
        $this->assertThrows(function () use ($params) {
            User::create($params);
        });
    }

    /**
     * @testdox 名前が長すぎるケース
     * @group model
     * @dataProvider tooLongNameProvider
     */
    public function test_cannot_create_when_name_is_too_long(string $name, string $email, string $password): void
    {
        $params = compact('name', 'email', 'password');
        $this->assertThrows(function () use ($params) {
            User::create($params);
        });
    }

    public static function tooLongNameProvider(): array
    {
        return [
            [str_repeat("a", 256), "b", "c"],
            [str_repeat("\u{1f37a}", 256), "b", "c"],
        ];
    }
}
