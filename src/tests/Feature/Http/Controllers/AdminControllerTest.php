<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
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
        $response->assertStatus(200);
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
        foreach (Contact::limit(10) as $contact) {
            $response->assertSeeText($contact->detail);
        }
        foreach (Contact::offset(11) as $contact) {
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
        $response = $this->delete('/admin');
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
        $contact = Contact::random();
        $this->actingAs($user)->delete('/admin', ['id' => $contact->id]);
        $this->assertNull(Contact::find($contact->id));
    }

    /**
     * @testdox [DELETE /admin] [contact exists] /admin へリダイレクト
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_existing_contact_redirects_to_admin(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $contact = Contact::random();
        $response = $this->actingAs($user)->delete('/admin', ['id' => $contact->id]);
        $response->assertRedirect('/admin');
    }

    /**
     * @testdox [DELETE /admin] [contact exists] contacts テーブルから削除
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_nonexisting_contact_does_nothing_to_contacts_table(): void
    {
        self::storeTestData();
        $contactCount = Contact::count();
        $user = User::create(RegisterTest::makeRegisterData());
        $nonexistingId = 999;
        while (!is_null(Contact::find($nonexistingId))) $nonexistingId++;
        $this->actingAs($user)->delete('/admin', ['id' => $nonexistingId]);
        $this->assertDatabaseCount('contacts', $contactCount);
    }

    /**
     * @testdox [DELETE /admin] [contact exists] /admin へリダイレクト
     * @group admin
     */
    public function test_delete_to_admin_for_admin_with_nonexisting_contact_redirects_to_admin(): void
    {
        self::storeTestData();
        $user = User::create(RegisterTest::makeRegisterData());
        $nonexistingId = 999;
        while (!is_null(Contact::find($nonexistingId))) $nonexistingId++;
        $response = $this->actingAs($user)->delete('/admin', ['id' => $nonexistingId]);
        $response->assertRedirect('/admin');
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

        Carbon::setTestNowAndTimezone('2023-01-10T09:00:00', 'Asia/Tokyo');
        $twoDaysAgo = Carbon::today()->subDays(2);
        $yesterday = Carbon::yesterday();
        $today = Carbon::today();

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
}
