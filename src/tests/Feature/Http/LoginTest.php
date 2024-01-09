<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @testdox [GET /login] [未認証] ステータスコード 200
     * @group login
     */
    public function test_get_to_login_for_guest_users_returns_status_code_200(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /login] [未認証] ビュー login を表示
     * @group login
     */
    public function test_get_to_login_for_guest_users_renders_view_login(): void
    {
        $response = $this->get('/login');
        $response->assertViewIs('login');
    }

    /**
     * @testdox [GET /login] [未認証] 検証エラー無し
     * @group login
     */
    public function test_get_to_login_for_guest_users_causes_no_validation_errors(): void
    {
        $response = $this->get('/login');
        $response->assertValid();
    }

    /**
     * @testdox [GET /login] [認証済み] ステータスコード 200
     * @group login
     */
    public function test_get_to_login_for_authenticated_users_redirects_to_admin(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/login');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /login] [認証済み] 検証エラー無し
     * @group login
     */
    public function test_get_to_login_for_authenticated_users_causes_no_validation_errors(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/login');
        $response->assertValid();
    }

    /**
     * @testdox [POST /login] [authentication success] /admin へリダイレクト
     * @group login
     */
    public function test_post_to_login_for_registered_user_with_right_password_redirects_to_admin(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $data = $user->only(['email', 'password']);
        $response = $this->post('/login', $data);
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [POST /login] [authentication success] ユーザが認証済みになる
     * @group login
     */
    public function test_post_to_login_for_registered_user_with_right_password_authenticates_current_user(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $data = $user->only(['email', 'password']);
        $this->post('/login', $data);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @testdox [POST /login] [authentication success] 検証エラー無し
     * @group login
     */
    public function test_post_to_login_for_registered_user_with_right_password_causes_no_validation_error(): void
    {
        $data = RegisterTest::makeRegisterData();
        User::create($data);
        $response = $this->post('/login', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /login] [authentication failure] /login へリダイレクト
     * @group login
     */
    public function test_post_to_login_for_registered_user_with_wrong_password_redirects_to_login(): void
    {
        $data = RegisterTest::makeRegisterData();
        User::create($data);
        $data['password'] .= 'a';
        $response = $this->post('/login', $data);
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /login] [authentication failure] 検証エラー有り
     * @group login
     */
    public function test_post_to_login_for_registered_user_with_wrong_password_causes_authentication_error(): void
    {
        $data = RegisterTest::makeRegisterData();
        User::create($data);
        $data['password'] .= 'a';
        $response = $this->post('/login', $data);
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /login] [unregistered user] /login へリダイレクト
     * @group login
     */
    public function test_post_to_login_for_unregistered_user_redirects_to_login(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = self::makeLoginData();
        $response = $this->post('/login', $data);
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /login] [unregistered user] 検証エラー有り
     * @group login
     */
    public function test_post_to_login_for_unregistered_user_causes_authentication_error(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = self::makeLoginData();
        $response = $this->post('/login', $data);
        $response->assertInvalid('email');
    }

    /**
     * @testdox [POST /login] [empty data] /login へリダイレクト
     * @group login
     */
    public function test_post_to_login_with_empty_data_redirects_to_login(): void
    {
        $response = $this->post('/login');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /login] [empty data] 検証エラー無し
     * @group login
     */
    public function test_post_to_login_with_empty_data_causes_validation_errors(): void
    {
        $response = $this->post('/login');
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /login] [$name is $kind] /login へリダイレクト
     * @group login
     * @dataProvider loginDataProvider
     */
    public function test_post_to_login_with_invalid_parameter_redirects_to_login(
        string $name,
        string $value,
        string $kind,
        string $message,
    ): void {
        $data = self::makeLoginData();
        $data[$name] = $value;
        $response = $this->post('/login', $data);
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /login] [$name is $kind] 検証エラー有り
     * @group login
     * @dataProvider loginDataProvider
     */
    public function test_post_to_login_with_invalid_parameter_causes_validation_error(
        string $name,
        string $value,
        string $kind,
        string $message,
    ): void {
        $data = self::makeLoginData();
        $data[$name] = $value;
        $response = $this->post('/login', $data);
        $response->assertInvalid([$name => $message]);
    }

    public static function makeLoginData(): array
    {
        return [
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];
    }

    public static function loginDataProvider(): array
    {
        return [
            ['email',    '',                   'empty',     'メールアドレスを入力してください'],
            ['email',    'a',                  'not email', 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください'],
            ['email',    str_repeat('a', 255), 'too long',  'メールアドレスは「ユーザー名@ドメイン」形式で入力してください'],
            ['password', '',                   'empty',     'パスワードを入力してください'],
            ['password', str_repeat('a', 256), 'too long',  'パスワードは255文字以内で入力してください'],
        ];
    }
}
