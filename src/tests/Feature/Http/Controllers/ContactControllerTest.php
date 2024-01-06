<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Models\ContactTest;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @testdox 「GET `/`」がステータスコード200を返す */
    public function test_http_get_index_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @testdox 「GET `/`」で `view('index')` が表示される */
    public function test_http_get_index_renders_index_view(): void
    {
        $response = $this->get('/');
        $response->assertViewIs('index');
    }

    /** @testdox 「GET `/`」で検証エラー無し */
    public function test_http_get_index_is_valid(): void
    {
        $response = $this->get('/');
        $response->assertValid();
    }

    /**
     * @testdox 「POST `/confirm`」で入力必須項目が足りない
     * @testWith ["last_name", "姓を入力してください"]
     *           ["first_name", "名を入力してください"]
     *           ["gender", "性別を選択したください"]
     *           ["email", "メールアドレスを入力してください"]
     *           ["area_code", "電話番号を入力してください"]
     *           ["city_code", "電話番号を入力してください"]
     *           ["subscriber_code", "電話番号を入力してください"]
     *           ["address", "住所を入力してください"]
     *           ["category_id", "お問い合わせの種類を選択してください"]
     *           ["detail", "お問い合わせ内容を入力してください"]
     */
    public function test_http_post_confirm_sends_error_when_param_is_missing(
        string $paramName,
        string $errorMessage,
    ): void {
        $data = ContactTest::normalParams();
        $data[$paramName] = null;
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid([$paramName => $errorMessage]);
    }

    /**
     * @testdox 「POST `/confirm`」でメールアドレスの形式が間違っている
     */
    public function test_http_post_confirm_sends_error_when_email_format_is_incorrect(): void
    {
        $data = ContactTest::normalParams();
        $data['email'] = 'test';
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid(['email' => 'メールアドレスはメール形式で入力してください']);
    }

    /**
     * @testdox 「POST `/confirm`」で電話番号の形式ば間違っている
     * @testWith ["aaa", "1234", "5789", "area_code"]
     *           ["080123", "1234", "5789", "area_code"]
     *           ["080", "aaa", "5789", "city_code"]
     *           ["080", "123456", "5789", "city_code"]
     *           ["080", "1234", "aaa", "subscriber_code"]
     *           ["080", "1234", "578901", "subscriber_code"]
     */
    public function test_http_post_confirm_sends_error_when_tel_format_is_incorrect(
        string $areaCode,
        string $cityCode,
        string $subscriberCode,
        string $incorrectParamName,
    ): void {
        $data = ContactTest::normalParams();
        $data['area_code'] = $areaCode;
        $data['city_code'] = $cityCode;
        $data['subscriber_code'] = $subscriberCode;
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid([$incorrectParamName => '電話番号は5桁までの数字で入力してください']);
    }

    /**
     * @testdox 「POST `/confirm`」でお問い合わせ内容が120文字より長い
     */
    public function test_http_post_confirm_sends_error_when_detail_is_too_long(): void
    {
        $data = ContactTest::normalParams();
        $data['detail'] = str_repeat('A', 121);
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid(['detail' => 'お問合せ内容は120文字以内で入力してください']);
    }

    public function normalFormData(): array
    {
        $data = ContactTest::normalParams();
        unset($data['tel']);
        $data['area_code'] = '080';
        $data['city_code'] = '1234';
        $data['subscriber_code'] = '5678';
        return $data;
    }
}
