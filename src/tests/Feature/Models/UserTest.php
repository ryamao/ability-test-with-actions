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
     */
    public function tes_can_create(): void
    {
        $data = self::normalData();
        $user = User::create($data);
        $this->assertNotNull($user);
    }

    /**
     * @testdox 必要なカラム $column が不足しているケース
     * @group model
     * @testWith ["name"]
     *           ["email"]
     *           ["password"]
     */
    public function test_cannot_create(string $column): void
    {
        $data = self::normalData();
        unset($data[$column]);
        $this->assertThrows(function () use ($data) {
            User::create($data);
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

    public static function normalData(): array
    {
        return [
            'name' => 'foo',
            'email' => 'test@example.com',
            'password' => 'qwerty',
        ];
    }

    public static function tooLongNameProvider(): array
    {
        return [
            [str_repeat("a", 256), "b", "c"],
            [str_repeat("\u{1f37a}", 256), "b", "c"],
        ];
    }
}
