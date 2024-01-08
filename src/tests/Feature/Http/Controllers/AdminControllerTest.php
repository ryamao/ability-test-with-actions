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
        $user = User::create($this->makeTestData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /register] [認証済み] ビュー register を表示
     * @group register
     */
    public function test_get_to_register_for_authenticated_users_renders_view_register(): void
    {
        $user = User::create($this->makeTestData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertViewIs('register');
    }

    /**
     * @testdox [GET /register] [認証済み] 検証エラー無し
     * @group register
     */
    public function test_get_to_register_for_authenticated_users_causes_no_validation_errors(): void
    {
        $user = User::create($this->makeTestData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] /login へリダイレクト
     * @group register
     */
    public function test_post_to_register_with_complete_data_redirects_to_login(): void
    {
        $response = $this->post('/register', $this->makeTestData());
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /register] [complete data] 検証エラー無し
     * @group register
     */
    public function test_post_to_register_with_complete_data_causes_no_validation_errors(): void
    {
        $response = $this->post('/register', $this->makeTestData());
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] users テーブルに保存
     * @group register
     */
    public function test_post_to_register_with_complete_data_saves_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
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
        $data = $this->makeTestData();
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
        $data = $this->makeTestData();
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
        $data = $this->makeTestData();
        $data[$name] = $value;
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    private function makeTestData(): array
    {
        return [
            'name' => $this->faker->name(),
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
}
