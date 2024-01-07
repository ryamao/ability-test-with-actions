<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdox 作成可能なケース
     * @group model
     */
    public function test_can_create(): void
    {
        $params = self::normalParams();
        $contact = Contact::create($params);
        $this->assertNotNull($contact);
    }

    /**
     * @testdox 必須でないカラム $column が指定されていないケース
     * @group model
     * @testWith ["category_id"]
     *           ["building"]
     */
    public function test_can_create_when_non_required_column_is_missing(string $column): void
    {
        $params = self::normalParams();
        unset($params[$column]);
        $contact = Contact::create($params);
        $this->assertNotNull($contact);
    }

    /**
     * @testdox 必要なカラム $column が指定されていないケース
     * @group model
     * @testWith ["first_name"]
     *           ["last_name"]
     *           ["email"]
     *           ["tel"]
     *           ["address"]
     *           ["detail"]
     */
    public function test_cannot_create_when_required_column_is_missing(string $column): void
    {
        $params = self::normalParams();
        unset($params[$column]);
        $this->assertThrows(function () use ($params) {
            Contact::create($params);
        });
    }

    public static function normalParams(): array
    {
        return [
            'category_id' => Category::create(['content' => 'aaa'])->id,
            'first_name' => 'foo',
            'last_name' => 'bar',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '0123456789',
            'address' => 'hoge',
            'building' => 'fuga',
            'detail' => 'zzz',
        ];
    }
}
