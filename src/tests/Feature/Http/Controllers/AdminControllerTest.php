<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Gender;
use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Http\RegisterTest;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @testdox [GET /admin] [guest user] /login へリダイレクト
     * @group admin
     */
    public function test_get_to_admin_for_guest_user_redirects_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [GET /admin] [admin user] [empty query] ステータスコード200
     * @group admin
     */
    public function test_get_to_admin_for_admin_with_empty_query_returns_status_code_200(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin');
        $response->assertOk();
    }

    /**
     * @testdox [GET /admin] [admin user] [empty query] categories テーブルの内容を表示
     * @group admin
     */
    public function test_get_to_admin_for_admin_with_empty_query_renders_categories(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin');
        foreach (Category::all() as $category) {
            $response->assertSee('value="' . $category->id . '"', escape: false);
            $response->assertSeeText($category->content);
        }
    }

    /**
     * @testdox [GET /admin] [admin user] [empty query] contacts テーブルが0件
     * @group admin
     */
    public function test_get_to_admin_for_admin_with_empty_query_renders_contacts_with_0_records(): void
    {
        self::storeTestData(0);
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin');
        $response->assertDontSee('class="admin__contact-item"', escape: false);
    }

    /**
     * @testdox [GET /admin] [admin user] [empty query] contacts テーブルが$n件
     * @group admin
     * @testWith [1]
     *           [9]
     *           [10]
     *           [11]
     */
    public function test_get_to_admin_for_admin_with_empty_query_renders_contacts_with_n_records(int $n): void
    {
        self::storeTestData($n);
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin');
        $contacts = Contact::orderBy('created_at')->get();
        foreach ($contacts->take(10) as $contact) {
            $response->assertSeeText($contact->detail);
        }
        foreach ($contacts->skip(10) as $contact) {
            $response->assertDontSeeText($contact->detail);
        }
    }

    /**
     * @testdox [GET /admin] [admin user] [search $search] 検索結果が1ページ分になるケース
     * @group admin
     * @testWith ["田", 9]
     *           ["田 太", 3]
     *           ["[原]", 1]
     *           ["Taro", 2]
     *           ["%example_", 1]
     *           ["FOO", 0]
     */
    public function test_get_to_admin_for_admin_with_search_query_renders_single_page(string $search, int $n): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin?search=' . urlencode($search));
        $response->assertViewHas('contacts', function ($paginator) use ($n) {
            return $paginator->count() === $n;
        });
    }

    /**
     * @testdox [GET /admin] [admin user] [search $search] 検索結果が2ページ分になるケース
     * @group admin
     * @testWith ["", 3]
     *           ["@example2.com", 1]
     */
    public function test_get_to_admin_for_admin_with_search_query_renders_multi_pages(string $search, int $n): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin?search=' . urlencode($search));
        $response->assertViewHas('contacts', function ($paginator) {
            return $paginator->count() === 10;
        });
        $response = $this->actingAs($user)->get('/admin?search=' . urlencode($search) . '&page=2');
        $response->assertViewHas('contacts', function ($paginator) use ($n) {
            return $paginator->count() === $n;
        });
    }

    /**
     * @testdox [GET /admin] [admin user] [gender is $gender] 性別で検索
     * @group admin
     * @testWith [1]
     *           [2]
     *           [3]
     */
    public function test_get_to_admin_for_admin_with_gender_query(int $gender): void
    {
        self::storeTestData();
        $n = Contact::where('gender', '=', $gender)->count();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin?gender=' . $gender);
        $response->assertViewHas('contacts', function ($paginator) use ($n) {
            return $paginator->count() === $n;
        });
    }

    /**
     * @testdox [GET /admin] [admin user] [category query] お問い合わせの種類で検索
     * @group admin
     */
    public function test_get_to_admin_for_admin_with_category_query(): void
    {
        self::storeTestData();
        $category = Category::first();
        $n = Contact::where('category_id', '=', $category->id)->count();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin?category=' . $category->id);
        $response->assertViewHas('contacts', function ($paginator) use ($n) {
            return $paginator->count() === $n;
        });
    }

    /**
     * @testdox [GET /admin] [admin user] [date query] 日付で検索
     * @group admin
     * @testWith ["2023-01-10", 2]
     *           ["2023-01-09", 7]
     *           ["2023-01-08", 4]
     */
    public function test_get_to_admin_for_admin_with_date_query(string $dateString, int $count): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin?date=' . $dateString);
        $response->assertViewHas('contacts', function ($paginator) use ($count) {
            return $paginator->count() === $count;
        });
    }

    /**
     * @testdox [DELETE /admin] [guest user] /login へリダイレクト
     * @group admin
     */
    public function test_delete_to_admin_for_guest_redirects_to_login(): void
    {
        $this->assertDatabaseEmpty('contacts');
        $response = $this->delete('/admin/1');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [DELETE /admin] [contact exists] contacts テーブルから削除
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_existing_contact_deletes_from_contacts_table(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $contact = Contact::all()->random();
        $this->actingAs($user)->delete("/admin/{$contact->id}");
        $this->assertTrue(Contact::where('id', '=', $contact->id)->doesntExist());
    }

    /**
     * @testdox [DELETE /admin] [contact exists] /admin へリダイレクト
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_existing_contact_redirects_to_admin(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $contact = Contact::all()->random();
        $response = $this->actingAs($user)->delete("/admin/{$contact->id}");
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [DELETE /admin] [contact not exists] contacts テーブルに変化無し
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_nonexisting_contact_does_nothing_to_contacts_table(): void
    {
        self::storeTestData();
        $contactCount = Contact::count();
        $user = User::create(RegisterTest::makeRegisterData());
        $nonexistingId = 999;
        while (!is_null(Contact::find($nonexistingId))) $nonexistingId++;
        $this->actingAs($user)->delete("/admin/{$nonexistingId}");
        $this->assertDatabaseCount('contacts', $contactCount);
    }

    /**
     * @testdox [DELETE /admin] [contact not exists] /admin へリダイレクト
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_nonexisting_contact_redirects_to_admin(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $nonexistingId = 999;
        while (!is_null(Contact::find($nonexistingId))) $nonexistingId++;
        $response = $this->actingAs($user)->delete("/admin/{$nonexistingId}");
        $response->assertNotFound();
    }

    /**
     * @testdox [POST /logout] [guest user] /login へリダイレクト
     * @group admin
     */
    public function test_post_to_login_for_guest_redirects_to_login(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [POST /logout] [admin user] /login へリダイレクト
     * @group admin
     */
    public function test_post_to_login_for_admin_redirects_to_login(): void
    {
        $user = User::create(RegisterTest::makeRegisterData());
        $this->actingAs($user)->get('/admin');
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [GET /admin/export] [guest user] /login へリダイレクト
     * @group admin
     */
    public function test_get_to_admin_export_for_guest_redirects_to_login(): void
    {
        $response = $this->get('/admin/export');
        $response->assertRedirect('/login');
    }

    /**
     * @testdox [GET /admin/export] [admin user] contacts テーブルが空
     * @group admin
     */
    public function test_get_to_admin_export_for_admin_returns_empty_file_when_contacts_table_is_empty(): void
    {
        $this->assertDatabaseEmpty('contacts');
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin/export');
        $response->assertDownload();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->expectOutputString(
            implode(PHP_EOL, [
                "contact_id,last_name,first_name,gender,gender_name,email,address,building,category_id,category_content,detail,created_at,updated_at",
                "",
            ])
        );
        $response->getCallback()();
    }

    /**
     * @testdox [GET /admin/export] [admin user] クエリストリング無し
     * @group admin
     */
    public function test_get_to_admin_export_for_admin_with_empty_query_returns_whole_data(): void
    {
        $expectedCsv = self::storeTestDataForExport();
        $user = User::create(RegisterTest::makeRegisterData());
        $response = $this->actingAs($user)->get('/admin/export');
        $response->assertDownload();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->expectOutputString($expectedCsv);
        $response->getCallback()();
    }

    public static function storeTestData(?int $contactCount = null): void
    {
        $contents = [
            'a',
            'b',
            'c',
        ];
        foreach ($contents as $content) {
            Category::create(compact('content'));
        }
        $categories = Category::all();

        CarbonImmutable::setTestNowAndTimezone('2023-01-10T09:00:00', 'Asia/Tokyo');
        $twoDaysAgo = CarbonImmutable::today()->subDays(2);
        $yesterday = CarbonImmutable::yesterday();
        $today = CarbonImmutable::today();

        $records = [
            ['原', '太郎', 1, 'taro@example.com', $twoDaysAgo],
            ['原田', '良子', 2, 'abcdefghijklmnopqrstuvwxyz@example.com', $twoDaysAgo],
            ['田原', '良太', 1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ@example2.com', $twoDaysAgo],
            ['小田', '良', 3, '0123456789@example2.com', $twoDaysAgo],
            ['小田原', '田悦子', 2, '!#$%&\'*+-/=?^_{|}~`@example2.com', $yesterday],
            ['小原', '次郎', 1, 'a.b@example2.com', $yesterday],
            ['小原田', '花子', 2, '"abcdefghijklmnopqrstuvwxyz"@example2.com', $yesterday],
            ['多田', '花子', 2, '"!#$%&\'*+-/=?^_{|}~`"@example2.com', $yesterday],
            ['多田野', '花', 1, '".."@example2.com', $yesterday],
            ['太田', '🍣', 1, '" ()*,:;<>@[]"@example2.com', $yesterday],
            ['太田', '🍺', 1, '"\\a\\A\\0\\!\\."@example2.com', $yesterday],
            ['大多', '大', 3, '"' . '\\"' . '\\ ' . "\\\t" . '"' . '@example2.com', $today],
            ['Yamada', 'Taro', 1, '%example_.com@example2.com', $today],
        ];
        $contactCount = $contactCount ?? count($records);
        $records = array_slice($records, 0, $contactCount);

        foreach ($records as [$last_name, $first_name, $gender, $email, $datetime]) {
            $created_at = $datetime;
            $updated_at = $datetime;
            $contact = Contact::factory()
                ->recycle($categories)
                ->create(compact('last_name', 'first_name', 'gender', 'email', 'created_at', 'updated_at'));
        }
    }

    public static function storeTestDataForExport(): string
    {
        foreach (range(1, 3) as $i) {
            Category::create(['content' => fake()->realText()]);
        }
        $categories = Category::all();

        $csvStream = fopen('php://memory', 'r+');
        fputcsv($csvStream, ['contact_id', 'last_name', 'first_name', 'gender', 'gender_name', 'email', 'address', 'building', 'category_id', 'category_content', 'detail', 'created_at', 'updated_at']);

        foreach (range(1, 5) as $i) {
            $gender = fake()->numberBetween(1, 3);
            $genderName = match ($gender) {
                1 => 'male',
                2 => 'female',
                3 => 'other'
            };
            $last_name = fake()->lastName($genderName);
            $first_name = fake()->firstName($genderName);
            $email = fake()->email();
            $tel = fake()->phoneNumber();
            $address = fake()->address();
            $building = fake()->buildingNumber();
            $category = $categories->random();
            $category_id = $category->id;
            $categoryContent = $category->content;
            $detail = implode(PHP_EOL, fake()->sentences(2));
            $contact = Contact::create(compact('last_name', 'first_name', 'gender', 'email', 'tel', 'address', 'building', 'category_id', 'detail'));
            $createdAt = $contact->created_at->format('Y-m-d H:i:s');
            $updatedAt = $contact->updated_at->format('Y-m-d H:i:s');
            fputcsv($csvStream, [$contact->id, $last_name, $first_name, $gender, Gender::from($gender)->name(), $email, $address, $building, $category_id, $categoryContent, $detail, $createdAt, $updatedAt]);
        }

        rewind($csvStream);
        $csvString = '';
        while (!feof($csvStream)) $csvString .= fgets($csvStream);
        fclose($csvStream);

        return $csvString;
    }
}
