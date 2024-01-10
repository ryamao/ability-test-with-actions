<?php

namespace Tests\Feature\View;

use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * @testdox input 要素の name 属性の値が $name
     * @group login
     * @testWith ["email"]
     *           ["password"]
     */
    public function test_parameter_name_exists_in_name_attribute_value(string $name): void
    {
        $view = $this->withViewErrors([])->view('login');
        $view->assertSee('name="' . $name . '"', escape: false);
    }

    /**
     * @testdox デフォルトでは、エラーメッセージ $message が表示されない。
     * @group login
     * @testWith ["メールアドレスを入力してください"]
     *           ["メールアドレスは「ユーザー名@ドメイン」形式で入力してください"]
     *           ["パスワードを入力してください"]
     *           ["パスワードは255文字以内で入力してください"]
     */
    public function test_no_error_message_is_displayed_by_default(string $message): void
    {
        $view = $this->withViewErrors([])->view('login');
        $view->assertDontSeeText($message);
    }

    /**
     * @testdox パラメータ $name のエラーメッセージ $message が表示される
     * @group login
     * @testWith ["email", "メールアドレスを入力してください"]
     *           ["password", "パスワードを入力してください"]
     */
    public function test_error_message_is_displayed_for_parameter(string $name, string $message): void
    {
        $view = $this->withViewErrors([$name => $message])->view('login');
        $view->assertSeeText($message);
    }
}
