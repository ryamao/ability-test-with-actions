<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Contact;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @testdox 作成可能なケース
     * @group model
     */
    public function test_can_create(): void
    {
        $data = $this->makeTestData();
        $contact = Contact::create($data);
        $this->assertNotNull($contact);
    }

    /**
     * @testdox 必須でないカラム $column が指定されていないケース
     * @group model
     * @testWith ["building"]
     */
    public function test_can_create_when_non_required_column_is_missing(string $column): void
    {
        $data = $this->makeTestData();
        unset($data[$column]);
        $contact = Contact::create($data);
        $this->assertNotNull($contact);
    }

    /**
     * @testdox 必要なカラム $column が指定されていないケース
     * @group model
     * @testWith ["category_id"]
     *           ["first_name"]
     *           ["last_name"]
     *           ["email"]
     *           ["tel"]
     *           ["address"]
     *           ["detail"]
     */
    public function test_cannot_create_when_required_column_is_missing(string $column): void
    {
        $data = $this->makeTestData();
        unset($data[$column]);
        $this->assertThrows(function () use ($data) {
            Contact::create($data);
        });
    }

    /**
     * @testdox easternOrdredName
     * @group model
     */
    public function test_eastern_ordered_name(): void
    {
        $data = $this->makeTestData();
        $contact = Contact::create($data);
        $this->assertSame(
            $data['last_name'] . '　' . $data['first_name'],
            $contact->easternOrderedName(),
        );
    }

    /**
     * @testdox genderName: $gender => $genderName
     * @group model
     * @testWith [1, "男性"]
     *           [2, "女性"]
     *           [3, "その他"]
     */
    public function test_gender_name(int $gender, string $genderName): void
    {
        $data = $this->makeTestData();
        $data['gender'] = $gender;
        $contact = Contact::create($data);
        $this->assertSame($genderName, $contact->genderName());
    }

    /**
     * @testdox category
     * @group model
     */
    public function test_category(): void
    {
        $data = $this->makeTestData();
        $category = Category::create(['content' => $this->faker->text()]);
        $data['category_id'] = $category->id;
        $contact = Contact::create($data);
        $this->assertTrue($contact->category->is($category));
    }

    /**
     * @testdox partialMatch('$keyword') => $matchCount件
     * @group model
     * @testWith ["田", 2]
     *           ["山田", 1]
     *           ["中", 1]
     *           ["太", 1]
     *           ["郎", 3]
     *           ["三郎", 1]
     *           ["test2", 1]
     *           ["example", 4]
     *           ["4", 1]
     *           ["foo", 0]
     *           ["", 4]
     *           ["山_", 0]
     *           ["test%", 0]
     *           ["'", 0]
     *           ["\"", 0]
     */
    public function test_partial_match(string $keyword, int $matchCount): void
    {
        $this->storeTestDataForSearch([
            ['山田', '太郎', 'test1@example.com'],
            ['山中', '次郎', 'test2@example.com'],
            ['田原', '三郎', 'test3@example.com'],
            ['TEST4', 'Test4', 'test4@example.com'],
        ]);
        $contacts = Contact::partialMatch($keyword)->get();
        $this->assertCount($matchCount, $contacts);
    }

    /**
     * @testdox exactMatch('$keyword') => $matchCount件
     * @group model
     * @testWith ["田", 0]
     *           ["原", 1]
     *           ["田原", 1]
     *           ["原田", 0]
     *           ["太", 0]
     *           ["太郎", 1]
     *           ["test2", 0]
     *           ["test2@example.com", 1]
     *           ["test4", 1]
     *           ["foo", 0]
     *           ["", 0]
     *           ["_郎", 0]
     *           ["test%", 0]
     */
    public function test_exact_match(string $keyword, int $matchCount): void
    {
        $this->storeTestDataForSearch([
            ['山田', '太郎', 'test1@example.com'],
            ['原', '次郎', 'test2@example.com'],
            ['田原', '三郎', 'test3@example.com'],
            ['TEST4', 'Test4', 'test4@example.com'],
        ]);
        $contacts = Contact::exactMatch($keyword)->get();
        $this->assertCount($matchCount, $contacts);
    }

    /**
     * @testdox searchByKeywords('$keywords') => $matchCount件
     * @group model
     * @testWith ["田", 2]
     *           ["田 郎", 2]
     *           ["原　test3", 1]
     *           ["原 .com", 2]
     *           ["[原]", 1]
     *           ["原 [太郎]", 0]
     *           ["原　[次郎]", 1]
     *           ["[]", 0]
     *           ["", 4]
     *           [null, 4]
     */
    public function test_search_by_keywords(?string $keywords, int $matchCount): void
    {
        $this->storeTestDataForSearch([
            ['山田', '太郎', 'test1@example.com'],
            ['原', '次郎', 'test2@example.com'],
            ['田原', '三郎', 'test3@example.com'],
            ['TEST4', 'Test4', 'test4@example.com'],
        ]);
        $contacts = Contact::searchByKeywords($keywords)->get();
        $this->assertCount($matchCount, $contacts);
    }

    /**
     * @testdox searchByGender('$gender') => $matchCount件
     * @group model
     * @testWith [1, 2]
     *           [2, 3]
     *           [3, 1]
     *           [null, 6]
     */
    public function test_search_by_gender(?int $gender, int $matchCount): void
    {
        $this->storeTestDataForGender([1, 2, 3, 2, 2, 1]);
        $contacts = Contact::searchByGender($gender)->get();
        $this->assertCount($matchCount, $contacts);
    }

    /**
     * @testdox searchByCategory
     * @group model
     */
    public function test_search_by_category(): void
    {
        $category1 = Category::create(['content' => $this->faker->text()]);
        $category2 = Category::create(['content' => $this->faker->text()]);
        $category3 = Category::create(['content' => $this->faker->text()]);
        $categories = [
            $category1,
            $category2,
            $category1,
            $category3,
            $category1,
        ];
        foreach ($categories as $category) {
            $data = $this->makeTestData();
            $data['category_id'] = $category->id;
            Contact::create($data);
        }

        $contacts = Contact::searchByCategory($category1->id)->get();
        $this->assertCount(3, $contacts);

        $contacts = Contact::searchByCategory(null)->get();
        $this->assertCount(5, $contacts);
    }

    /**
     * @testdox searchByDate
     * @group model
     */
    public function test_search_by_date(): void
    {
        $today = CarbonImmutable::today();
        $yesterday = $today->subDay();
        $twoDaysAgo = $yesterday->subDay();
        $dates = [
            $twoDaysAgo->addHours(1),
            $twoDaysAgo->addHours(12),
            $twoDaysAgo->addHours(23),
            $yesterday->addHours(2),
            $yesterday->addHours(8),
            $yesterday->addHours(9),
            $yesterday->addHours(13),
            $today->addHours(0),
            $today->addHours(7),
        ];
        foreach ($dates as $date) {
            $data = $this->makeTestData();
            $contact = Contact::create($data);
            $contact->created_at = $date;
            $contact->updated_at = $date;
            $contact->save();
        }

        $contacts = Contact::searchByDate($yesterday)->get();
        $this->assertCount(4, $contacts);

        $contacts = Contact::searchByDate(null)->get();
        $this->assertCount(9, $contacts);
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
            'tel' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'building' => $this->faker->buildingNumber(),
            'detail' => $this->faker->text(),
        ];
    }

    private function storeTestDataForSearch(array $arrays): void
    {
        foreach ($arrays as [$last_name, $first_name, $email]) {
            $data = $this->makeTestData();
            $data = array_merge($data, compact('last_name', 'first_name', 'email'));
            Contact::create($data);
        }
    }

    private function storeTestDataForGender(array $array): void
    {
        foreach ($array as $gender) {
            $data = $this->makeTestData();
            $data['gender'] = $gender;
            Contact::create($data);
        }
    }
}
