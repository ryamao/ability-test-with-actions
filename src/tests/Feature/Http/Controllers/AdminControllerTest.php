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
    public function test_GET_to_register_for_guest_users_returns_status_code_200(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /register] [未認証] ビュー register を表示
     * @group register
     */
    public function test_GET_to_register_for_guest_users_renders_view_register(): void
    {
        $response = $this->get('/register');
        $response->assertViewIs('register');
    }

    /**
     * @testdox [GET /register] [未認証] 検証エラー無し
     * @group register
     */
    public function test_GET_to_register_for_guest_users_causes_no_validation_errors(): void
    {
        $response = $this->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [GET /register] [認証済み] /admin へリダイレクト
     * @group register
     */
    public function test_GET_to_register_for_authenticated_users_redirects_to_admin(): void
    {
        $user = User::create($this->makeTestData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [GET /register] [認証済み] 検証エラー無し
     * @group register
     */
    public function test_GET_to_register_for_authenticated_users_causes_no_validation_errors(): void
    {
        $user = User::create($this->makeTestData());
        $response = $this->actingAs($user)->get('/register');
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] /login へリダイレクト
     * @group register
     */
    public function test_POST_to_register_with_complete_data_redirects_to_login(): void
    {
        $response = $this->post('/register', $this->makeTestData());
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /register] [complete data] 検証エラー無し
     * @group register
     */
    public function test_POST_to_register_with_complete_data_causes_no_validation_errors(): void
    {
        $response = $this->post('/register', $this->makeTestData());
        $response->assertValid();
    }

    /**
     * @testdox [POST /register] [complete data] users テーブルに保存
     * @group register
     */
    public function test_POST_to_register_with_complete_data_saves_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
        $response = $this->post('/register', $data);
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('users', 1);
        $user = User::first();
        $this->assertSame($data['name'], $user->name);
        $this->assertSame($data['email'], $user->email);
        $this->assertSame(Hash::make($data['password']), $user->password);
    }

    /**
     * @testdox [POST /register] [empty data] /register へリダイレクト
     * @group register
     */
    public function test_POST_to_register_with_empty_data_redirects_to_register(): void
    {
        $response = $this->post('/register');
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [empty data] 認証エラー有り
     * @group register
     */
    public function test_POST_to_register_with_empty_data_causes_validation_errors(): void
    {
        $response = $this->post('/register');
        $errors = [];
        foreach (self::parameterNameAndErrorMessageProvider() as [$name, $message]) {
            $errors[$name] = $message;
        }
        $response->assertInvalid($errors);
    }

    /**
     * @testdox [POST /register] [empty data] users テーブルに変化無し
     * @group register
     */
    public function test_POST_to_register_with_empty_data_does_nothing_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $this->post('/register');
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] [$name is empty] /register へリダイレクト
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_lacked_parameter_redirects_to_register(string $name): void
    {
        $data = $this->makeTestData();
        $data[$name] = '';
        $response = $this->post('/register', $data);
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [$name is empty] 認証エラー有り
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_lacked_parameter_causes_validation_errors(string $name, string $message): void
    {
        $data = $this->makeTestData();
        $data[$name] = '';
        $response = $this->post('/register', $data);
        $response->assertInvalid([$name => $message]);
    }

    /**
     * @testdox [POST /register] [$name is empty] users テーブルに変化無し
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_lacked_parameter_does_nothing_to_users_table(string $name): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
        $data[$name] = '';
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] [$name is too long] /register へリダイレクト
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_too_long_parameter_redirects_to_register(string $name): void
    {
        $data = $this->makeTestData();
        $data[$name] = str_repeat('a', 256);
        $response = $this->post('/register', $data);
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [$name is too long] 認証エラー有り
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_too_long_parameter_causes_validation_errors(string $name, string $message): void
    {
        $data = $this->makeTestData();
        $data[$name] = str_repeat('a', 256);
        $response = $this->post('/register', $data);
        $response->assertInvalid([$name => $message]);
    }

    /**
     * @testdox [POST /register] [$name is too long] users テーブルに変化無し
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_too_long_parameter_does_nothing_to_users_table(string $name): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
        $data[$name] = str_repeat('a', 256);
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] [$name is array] /register へリダイレクト
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_array_parameter_redirects_to_register(string $name): void
    {
        $data = $this->makeTestData();
        $data[$name] = array_fill(0, 3, $data[$name]);
        $response = $this->post('/register', $data);
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [$name is array] 認証エラー有り
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_array_parameter_causes_validation_errors(string $name, string $message): void
    {
        $data = $this->makeTestData();
        $data[$name] = array_fill(0, 3, $data[$name]);
        $response = $this->post('/register', $data);
        $response->assertInvalid([$name => $message]);
    }

    /**
     * @testdox [POST /register] [$name is array] users テーブルに変化無し
     * @group register
     * @dataProvider parameterNameAndErrorMessageProvider
     */
    public function test_POST_to_register_with_array_parameter_does_nothing_to_users_table(string $name): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
        $data[$name] = array_fill(0, 3, $data[$name]);
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    /**
     * @testdox [POST /register] [email is not email] /register へリダイレクト
     * @group register
     */
    public function test_POST_to_register_with_invalid_email_redirects_to_register(): void
    {
        $data = $this->makeTestData();
        $data['email'] = 'abc';
        $response = $this->post('/register', $data);
        $response->assertRedirect('/register');
    }

    /**
     * @testdox [POST /register] [email is not email] 検証エラー有り
     * @group register
     */
    public function test_POST_to_register_with_invalid_email_causes_validation_error(): void
    {
        $data = $this->makeTestData();
        $data['email'] = 'abc';
        $response = $this->post('/register', $data);
        $response->assertInvalid(['email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください']);
    }

    /**
     * @testdox [POST /register] [email is not email] users テーブルに変化無し
     * @group register
     */
    public function test_POST_to_register_with_invalid_email_does_nothing_to_users_table(): void
    {
        $this->assertDatabaseEmpty('users');
        $data = $this->makeTestData();
        $data['email'] = 'abc';
        $this->post('/register', $data);
        $this->assertDatabaseEmpty('users');
    }

    private function makeTestData(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
    }

    public static function parameterNameAndErrorMessageProvider(): array
    {
        return [
            ['name', 'お名前を入力してください'],
            ['email', 'メールアドレスを入力してください'],
            ['password', 'パスワードを入力してください'],
        ];
    }
}
