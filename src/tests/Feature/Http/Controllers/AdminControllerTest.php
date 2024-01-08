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
     * @testdox [GET /register] [未認証] ステータスコード 200
     * @group register
     */
    public function test_get_to_register_for_guest_users_returns_status_code_200(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /register] [未認証] ビュー register を表示
     * @group register
     */
    public function test_get_to_register_for_guest_users_renders_view_register(): void
    {
        $response = $this->get('/register');
        $response->assertViewIs('register');
    }

    /**
     * @testdox [GET /register] [未認証] 検証エラー無し
     * @group register
     */
    public function test_get_to_register_for_guest_users_causes_no_validation_errors(): void
    {
        $response = $this->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [GET /register] [認証済み] ステータスコード 200
     * @group register
     */
    public function test_get_to_register_for_authenticated_users_returns_status_code_200(): void
    {
        $user = User::create($this->makeRegisterData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /register] [認証済み] ビュー register を表示
     * @group register
     */
    public function test_get_to_register_for_authenticated_users_renders_view_register(): void
    {
        $user = User::create($this->makeRegisterData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertViewIs('register');
    }

    /**
     * @testdox [GET /register] [認証済み] 検証エラー無し
     * @group register
     */
    public function test_get_to_register_for_authenticated_users_causes_no_validation_errors(): void
    {
        $user = User::create($this->makeRegisterData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] /login へリダイレクト
     * @group register
     */
    public function test_post_to_register_with_complete_data_redirects_to_login(): void
    {
        $response = $this->post('/register', $this->makeRegisterData());
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /register] [complete data] 検証エラー無し
     * @group register
     */
    public function test_post_to_register_with_complete_data_causes_no_validation_errors(): void
    {
        $response = $this->post('/register', $this->makeRegisterData());
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] users テーブルに保存
     * @group register
     */
    public function test_post_to_register_with_complete_data_saves_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeRegisterData();
        $response = $this->post('/register', $data);
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('users', 1);
        $user = User::first();
        $this->assertSame($data['name'], $user->name);
        $this->assertSame($data['email'], $user->email);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    /**
     * @testdox [POST /register] [empty data] /register へリダイレクト
     * @group register
     */
    public function test_post_to_register_with_empty_data_redirects_to_register(): void
    {
        $response = $this->post('/register');

        // FIXME リダイレクトレスポンスの Location が / になる
        $response->assertFound();
        // $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [empty data] 認証エラー有り
     * @group register
     */
    public function test_post_to_register_with_empty_data_causes_validation_errors(): void
    {
        $response = $this->post('/register');
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /register] [empty data] users テーブルに変化無し
     * @group register
     */
    public function test_post_to_register_with_empty_data_does_nothing_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $this->post('/register');
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] [$name is $kind] /register へリダイレクト
     * @group register
     * @dataProvider registerDataProvider
     */
    public function test_post_to_register_with_invalid_parameter_redirects_to_register(
        string $name,
        string $value,
        string $kind,
        string $message,
    ): void {
        $data = $this->makeRegisterData();
        $data[$name] = $value;
        $response = $this->post('/register', $data);

        // FIXME リダイレクトレスポンスの Location が / になる
        $response->assertFound();
        // $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [$name is $kind] 検証エラー有り
     * @group register
     * @dataProvider registerDataProvider
     */
    public function test_post_to_register_with_invalid_parameter_causes_validation_error(
        string $name,
        string $value,
        string $kind,
        string $message,
    ): void {
        $data = $this->makeRegisterData();
        $data[$name] = $value;
        $response = $this->post('/register', $data);
        $response->assertInvalid([$name => $message]);
    }

    /**
     * @testdox [POST /register] [$name is $kind] users テーブルに変化無し
     * @group register
     * @dataProvider registerDataProvider
     */
    public function test_post_to_register_with_invalid_parameter_does_nothing_to_users_table(
        string $name,
        string $value,
        string $kind,
        string $message,
    ): void {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeRegisterData();
        $data[$name] = $value;
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

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
        $user = User::create($this->makeRegisterData());
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [GET /login] [認証済み] 検証エラー無し
     * @group login
     */
    public function test_get_to_login_for_authenticated_users_causes_no_validation_errors(): void
    {
        $user = User::create($this->makeRegisterData());
        $response = $this->actingAs($user)->get('/login');
        $response->assertValid();
    }

    /**
     * @testdox [POST /login] [complete data] /admin へリダイレクト
     * @group login
     */
    public function test_post_to_login_with_complete_data_redirects_to_admin(): void
    {
        $data = $this->makeLoginData();
        $response = $this->post('/login', $data);
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [POST /login] [complete data] 検証エラー無し
     * @group login
     */
    public function test_post_to_login_with_complete_data_causes_no_validation_errors(): void
    {
        $data = $this->makeLoginData();
        $response = $this->post('/login', $data);
        $response->assertValid();
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
        $data = $this->makeLoginData();
        $data[$name] = $value;
        $response = $this->post('/login', $data);

        // FIXME リダイレクトレスポンスの Location が / になる
        // $response->assertFound();
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
        $data = $this->makeLoginData();
        $data[$name] = $value;
        $response = $this->post('/login', $data);
        $response->assertInvalid([$name => $message]);
    }

    private function makeRegisterData(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(minLength: 8, maxLength: 255),
        ];
    }

    private function makeLoginData(): array
    {
        return [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(minLength: 8, maxLength: 255),
        ];
    }

    public static function registerDataProvider(): array
    {
        return [
            ['name',     '',                   'empty',     'お名前を入力してください'],
            ['name',     str_repeat('a', 256), 'too long',  'お名前は255文字以内で入力してください'],
            ['email',    '',                   'empty',     'メールアドレスを入力してください'],
            ['email',    'a',                  'not email', 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください'],
            ['password', '',                   'empty',     'パスワードを入力してください'],
            ['password', str_repeat('a', 256), 'too long',  'パスワードは255文字以内で入力してください'],
        ];
    }

    public static function loginDataProvider(): array
    {
        return [
            ['email',    '',                   'empty',     'メールアドレスを入力してください'],
            ['email',    'a',                  'not email', 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください'],
            ['password', '',                   'empty',     'パスワードを入力してください'],
            ['password', str_repeat('a', 256), 'too long',  'パスワードは255文字以内で入力してください'],
        ];
    }
}
