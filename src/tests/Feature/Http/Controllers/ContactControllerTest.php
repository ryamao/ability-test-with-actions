<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Models\ContactTest;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @testdox [GET /] ステータスコード200
     * @group contact
     */
    public function test_http_get_index_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * @testdox [GET /] view('contact') を表示
     * @group contact
     */
    public function test_http_get_index_renders_contact_view(): void
    {
        $response = $this->get('/');
        $response->assertViewIs('contact');
    }

    /**
     * @testdox [GET /] 検証エラー無し
     * @group contact
     */
    public function test_http_get_index_is_valid(): void
    {
        $response = $this->get('/');
        $response->assertValid();
    }

    /**
     * @testdox [GET /confirm] ステータスコード405
     * @group confirm
     */
    public function test_http_get_confirm_returns_405(): void
    {
        $response = $this->get('/confirm');
        $response->assertStatus(405);
    }

    /**
     * @testdox [POST /confirm] すべての項目が入力されている場合、ステータスコード200
     * @group confirm
     */
    public function test_http_post_confirm_returns_200_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/confirm', $data);
        $response->assertStatus(200);
    }

    /**
     * @testdox [POST /confirm] すべての項目が入力されている場合、view('confirm') を表示
     * @group confirm
     */
    public function test_http_post_confirm_renders_confirm_view_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/confirm', $data);
        $response->assertViewIs('confirm');
    }

    /**
     * @testdox [POST /confirm] すべての項目が入力されている場合、検証エラー無し
     * @group confirm
     */
    public function test_http_post_confirm_is_valid_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/confirm', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /confirm] 建物名だけ入力されていない場合、ステータスコード200
     * @group confirm
     */
    public function test_http_post_confirm_returns_200_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/confirm', $data);
        $response->assertStatus(200);
    }

    /**
     * @testdox [POST /confirm] 建物名だけ入力されていない場合、view('confirm') を表示
     * @group confirm
     */
    public function test_http_post_confirm_renders_confirm_view_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/confirm', $data);
        $response->assertViewIs('confirm');
    }

    /**
     * @testdox [POST /confirm] 建物名だけ入力されていない場合、検証エラー無し
     * @group confirm
     */
    public function test_http_post_confirm_is_valid_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/confirm', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /confirm] 入力必須項目 $paramName が足りないケース
     * @group confirm
     * @testWith ["last_name", "姓を入力してください"]
     *           ["first_name", "名を入力してください"]
     *           ["gender", "性別を選択してください"]
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
        $data = $this->makeTestData();
        $data[$paramName] = null;
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid([$paramName => $errorMessage]);
    }

    /**
     * @testdox [POST /confirm] メールアドレスの形式が間違っているケース
     * @group confirm
     */
    public function test_http_post_confirm_sends_error_when_email_format_is_incorrect(): void
    {
        $data = $this->makeTestData();
        $data['email'] = 'test';
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid(['email' => 'メールアドレスはメール形式で入力してください']);
    }

    /**
     * @testdox [POST /confirm] 電話番号の形式が間違っているケース ($areaCode-$cityCode-$subscriberCode)
     * @group confirm
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
        $data = $this->makeTestData();
        $data['area_code'] = $areaCode;
        $data['city_code'] = $cityCode;
        $data['subscriber_code'] = $subscriberCode;
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid([$incorrectParamName => '電話番号は5桁までの数字で入力してください']);
    }

    /**
     * @testdox [POST /confirm] お問い合わせ内容が120文字より長いケース
     * @group confirm
     */
    public function test_http_post_confirm_sends_error_when_detail_is_too_long(): void
    {
        $data = $this->makeTestData();
        $data['detail'] = str_repeat('A', 121);
        $response = $this->post('/confirm', $data);
        $response->assertRedirect('/');
        $response->assertInvalid(['detail' => 'お問合せ内容は120文字以内で入力してください']);
    }

    /**
     * @testdox [GET /thanks] ステータスコード405
     * @group thanks
     */
    public function test_http_get_thanks_returns_405(): void
    {
        $response = $this->get('/thanks');
        $response->assertStatus(405);
    }

    /**
     * @testdox [POST /thanks] リクエストパラメータが無い場合、検証エラー有り
     * @group thanks
     */
    public function test_http_post_thanks_is_invalid_when_params_are_empty(): void
    {
        $response = $this->post('/thanks');
        $response->assertInvalid();
    }

    /**
     * @testdox [POST /thanks] リクエストパラメータが無い場合、`/` へリダイレクト
     * @group thanks
     */
    public function test_http_post_thanks_redirects_to_index_when_params_are_empty(): void
    {
        $response = $this->post('/thanks');
        $response->assertRedirect('/');
    }

    /**
     * @testdox [POST /thanks] リクエストパラメータが無い場合、`contacts` テーブルに変化無し
     * @group thanks
     */
    public function test_http_post_thanks_does_nothing_to_contacts_when_params_are_empty(): void
    {
        $this->assertDatabaseEmpty('contacts');
        $this->post('/thanks');
        $this->assertDatabaseEmpty('contacts');
    }

    /**
     * @testdox [POST /thanks] 入力必須項目 $paramName が足りない場合、検証エラー有り
     * @group thanks
     * @testWith ["last_name", "姓を入力してください"]
     *           ["first_name", "名を入力してください"]
     *           ["gender", "性別を選択してください"]
     *           ["email", "メールアドレスを入力してください"]
     *           ["area_code", "電話番号を入力してください"]
     *           ["city_code", "電話番号を入力してください"]
     *           ["subscriber_code", "電話番号を入力してください"]
     *           ["address", "住所を入力してください"]
     *           ["category_id", "お問い合わせの種類を選択してください"]
     *           ["detail", "お問い合わせ内容を入力してください"]
     */
    public function test_http_post_thanks_is_invalid_when_required_param_is_missing(string $paramName): void
    {
        $data = $this->makeTestData();
        $data[$paramName] = null;
        $response = $this->post('/thanks', $data);
        $response->assertInvalid($paramName);
    }

    /**
     * @testdox [POST /thanks] 入力必須項目 $paramName が足りない場合、`/` へリダイレクト
     * @group thanks
     * @testWith ["last_name", "姓を入力してください"]
     *           ["first_name", "名を入力してください"]
     *           ["gender", "性別を選択してください"]
     *           ["email", "メールアドレスを入力してください"]
     *           ["area_code", "電話番号を入力してください"]
     *           ["city_code", "電話番号を入力してください"]
     *           ["subscriber_code", "電話番号を入力してください"]
     *           ["address", "住所を入力してください"]
     *           ["category_id", "お問い合わせの種類を選択してください"]
     *           ["detail", "お問い合わせ内容を入力してください"]
     */
    public function test_http_post_thanks_redirects_to_index_when_required_param_is_missing(string $paramName): void
    {
        $data = $this->makeTestData();
        $data[$paramName] = null;
        $response = $this->post('/thanks', $data);
        $response->assertRedirect('/');
    }

    /**
     * @testdox [POST /thanks] 入力必須項目 $paramName が足りない場合、`contacts` テーブルに変化無し
     * @group thanks
     * @testWith ["last_name", "姓を入力してください"]
     *           ["first_name", "名を入力してください"]
     *           ["gender", "性別を選択してください"]
     *           ["email", "メールアドレスを入力してください"]
     *           ["area_code", "電話番号を入力してください"]
     *           ["city_code", "電話番号を入力してください"]
     *           ["subscriber_code", "電話番号を入力してください"]
     *           ["address", "住所を入力してください"]
     *           ["category_id", "お問い合わせの種類を選択してください"]
     *           ["detail", "お問い合わせ内容を入力してください"]
     */
    public function test_http_post_thanks_does_nothing_to_contacts_when_required_param_is_missing(string $paramName): void
    {
        $this->assertDatabaseEmpty('contacts');
        $data = $this->makeTestData();
        $data[$paramName] = null;
        $this->post('/thanks', $data);
        $this->assertDatabaseEmpty('contacts');
    }

    /**
     * @testdox [POST /thanks] すべての項目が入力されている場合、ステータスコード200
     * @group thanks
     */
    public function test_http_post_thanks_returns_200_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/thanks', $data);
        $response->assertStatus(200);
    }

    /**
     * @testdox [POST /thanks] すべての項目が入力されている場合、view('thanks') を表示
     * @group thanks
     */
    public function test_http_post_thanks_renders_thanks_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/thanks', $data);
        $response->assertViewIs('thanks');
    }

    /**
     * @testdox [POST /thanks] すべての項目が入力されている場合、検証エラー無し
     * @group thanks
     */
    public function test_http_post_thanks_is_valid_when_parameters_are_filled(): void
    {
        $data = $this->makeTestData();
        $response = $this->post('/thanks', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /thanks] すべての項目が入力されている場合、`contacts` テーブルに保存
     * @group thanks
     */
    public function test_http_post_thanks_stores_to_contacts_when_parameters_are_filled(): void
    {
        $this->assertDatabaseEmpty('contacts');
        $data = $this->makeTestData();
        $response = $this->post('/thanks', $data);
        $response->assertValid();
        $this->assertDatabaseCount('contacts', 1);
        $contact = Contact::first();
        $this->assertEquals($data['category_id'], $contact->category_id);
        $this->assertEquals($data['first_name'], $contact->first_name);
        $this->assertEquals($data['last_name'], $contact->last_name);
        $this->assertEquals($data['gender'], $contact->gender);
        $this->assertEquals($data['email'], $contact->email);
        $this->assertEquals($data['area_code'] . $data['city_code'] . $data['subscriber_code'], $contact->tel);
        $this->assertEquals($data['address'], $contact->address);
        $this->assertEquals($data['building'], $contact->building);
        $this->assertEquals($data['detail'], $contact->detail);
    }

    /**
     * @testdox [POST /thanks] 建物名だけ入力されていない場合、ステータスコード200
     * @group thanks
     */
    public function test_http_post_thanks_returns_200_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/thanks', $data);
        $response->assertStatus(200);
    }

    /**
     * @testdox [POST /thanks] 建物名だけ入力されていない場合、view('thanks') を表示
     * @group thanks
     */
    public function test_http_post_thanks_renders_thanks_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/thanks', $data);
        $response->assertViewIs('thanks');
    }

    /**
     * @testdox [POST /thanks] 建物名だけ入力されていない場合、検証エラー無し
     * @group thanks
     */
    public function test_http_post_thanks_is_valid_when_building_is_missing(): void
    {
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/thanks', $data);
        $response->assertValid();
    }

    /**
     * @testdox [POST /thanks] 建物名だけ入力されていない場合、`contacts` テーブルに保存
     * @group thanks
     */
    public function test_http_post_thanks_stores_to_contacts_when_building_is_missing(): void
    {
        $this->assertDatabaseEmpty('contacts');
        $data = $this->makeTestData();
        $data['building'] = null;
        $response = $this->post('/thanks', $data);
        $response->assertValid();
        $this->assertDatabaseCount('contacts', 1);
        $contact = Contact::first();
        $this->assertEquals($data['category_id'], $contact->category_id);
        $this->assertEquals($data['first_name'], $contact->first_name);
        $this->assertEquals($data['last_name'], $contact->last_name);
        $this->assertEquals($data['gender'], $contact->gender);
        $this->assertEquals($data['email'], $contact->email);
        $this->assertEquals($data['area_code'] . $data['city_code'] . $data['subscriber_code'], $contact->tel);
        $this->assertEquals($data['address'], $contact->address);
        $this->assertEquals($data['building'], $contact->building);
        $this->assertEquals($data['detail'], $contact->detail);
    }

    private function makeTestData(): array
    {
        $category = Category::create(['content' => $this->faker->text()]);
        return [
            'category_id' => $category->id,
            'first_name' => $this->faker->firstNameMale(),
            'last_name' => $this->faker->lastName(),
            'gender' => 1,
            'email' => $this->faker->email(),
            'area_code' => str_pad((string) $this->faker->numberBetween(0, 999), 3, '0', STR_PAD_LEFT),
            'city_code' => str_pad((string) $this->faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT),
            'subscriber_code' => str_pad((string) $this->faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT),
            'address' => $this->faker->address(),
            'building' => $this->faker->buildingNumber(),
            'detail' => $this->faker->realTextBetween(60, 120),
        ];
    }
}
