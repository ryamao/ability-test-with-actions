<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @testdox [GET /register] 未認証の場合、ステータスコード200
     * @group register
     */
    public function test_get_register_returns_200_when_user_is_guest(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /register] 未認証の場合、ビュー register を表示
     * @group register
     */
    public function test_get_register_renders_register_view_when_user_is_guest(): void
    {
        $response = $this->get('/register');
        $response->assertViewIs('register');
    }

    /**
     * @testdox [GET /register] 未認証の場合、検証エラー無し
     * @group register
     */
    public function test_get_register_is_valid_when_user_is_guest(): void
    {
        $response = $this->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [GET /register] 認証済みの場合、/admin へリダイレクト
     * @group register
     */
    public function test_get_register_redirects_to_admin_when_user_is_authenticated(): void
    {
        $user = User::create($this->normalData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [POST /register] リクエストパラメータに不足がない場合、認証エラー無し
     * @group register
     */
    public function test_post_register_is_valid_when_parameters_are_filled(): void
    {
        $data = $this->normalData();
        $response = $this->post('/register', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] リクエストパラメータに不足がない場合、/login へリダイレクト
     * @group register
     */
    public function test_post_register_redirects_to_login_when_parameters_are_filled(): void
    {
        $data = $this->normalData();
        $response = $this->post('/register', $data);
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /register] リクエストパラメータに不足がない場合、users テーブルに保存
     * @group register
     */
    public function test_post_register_stores_to_users_table_when_parameters_are_filled(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->normalData();
        $response = $this->post('/register', $data);
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('users', 1);
        $user = User::first();
        $this->assertSame($data['name'], $user->name);
        $this->assertSame($data['email'], $user->email);
        $this->assertSame(Hash::make($data['password']), $user->password);
    }

    /**
     * @testdox [POST /register] リクエストパラメータが空の場合、認証エラー有り
     * @group register
     */
    public function test_post_register_is_invalid_when_parameters_are_empty(): void
    {
        $response = $this->post('/register');
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /register] リクエストパラメータが空の場合、/register へリダイレクト
     * @group register
     */
    public function test_post_register_redirects_register_when_parameters_are_empty(): void
    {
        $response = $this->post('/register');
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] リクエストパラメータが空の場合、users テーブルに変化無し
     * @group register
     */
    public function test_post_register_does_nothing_to_users_table_when_parameters_are_empty(): void
    {
        $this->assertDatabaseEmpty('users');
        $this->post('/register');
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] 必須入力項目 $paramName が空の場合、認証エラー有り
     * @group register
     * @testWith ["name"]
     *           ["email"]
     *           ["password"]
     */
    public function test_post_register_is_invalid_when_required_parameter_is_missing(string $paramName): void
    {
        $data = $this->normalData();
        $data[$paramName] = null;
        $response = $this->post('/register', $data);
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /register] 必須入力項目 $paramName が空の場合、/register へリダイレクト
     * @group register
     * @testWith ["name"]
     *           ["email"]
     *           ["password"]
     */
    public function test_post_register_redirects_to_register_when_required_parameter_is_missing(string $paramName): void
    {
        $data = $this->normalData();
        $data[$paramName] = null;
        $response = $this->post('/register', $data);
        $response->assertRedirect();
    }

    /**
     * @testdox [POST /register] 必須入力項目 $paramName が空の場合、users テーブルに変化無し
     * @group register
     * @testWith ["name"]
     *           ["email"]
     *           ["password"]
     */
    public function test_post_register_does_nothing_users_table_when_required_parameter_is_missing(string $paramName): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->normalData();
        $data[$paramName] = null;
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    public function normalData(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
    }
}
