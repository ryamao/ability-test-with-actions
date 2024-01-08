<?php

namespace Tests\Feature\View;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * @testdox input 要素の name 属性の値が "name"
     * @group view register
     */
    public function test_name_exists_in_name_attribute_value(): void
    {
        $view = $this->view('register');
        $view->assertSee('name="name"', escape: false);
    }

    /**
     * @testdox input 要素の name 属性の値が "email"
     * @group view register
     */
    public function test_email_exists_in_name_attribute_value(): void
    {
        $view = $this->view('register');
        $view->assertSee('name="email"', escape: false);
    }

    /**
     * @testdox input 要素の name 属性の値が "password"
     * @group view register
     */
    public function test_password_exists_in_name_attribute_value(): void
    {
        $view = $this->view('register');
        $view->assertSee('name="password"', escape: false);
    }

    /**
     * @testdox デフォルトでは、エラーメッセージ $message が表示されない。
     * @group view register
     * @testWith ["お名前を入力してください"]
     *           ["メールアドレスを入力してください"]
     *           ["メールアドレスは「ユーザー名@ドメイン」形式で入力してください"]
     *           ["パスワードを入力してください"]
     */
    public function test_no_error_message_is_displayed_by_default(string $message): void
    {
        $view = $this->view('register');
        $view->assertDontSeeText($message);
    }

    /**
     * @testdox パラメータ $name のエラーメッセージ $message が表示される
     * @group view register
     * @testWith ["name", "お名前を入力してください"]
     *           ["email", "メールアドレスを入力してください"]
     *           ["password", "パスワードを入力してください"]
     */
    public function test_error_message_is_displayed_for_parameter(string $name, string $message): void
    {
        $view = $this->withViewErrors([$name => $message])
            ->view('register');
        $view->assertSeeText($message);
    }
}
