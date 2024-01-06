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
     * @testWith [null]
     *           ["category_id"]
     *           ["building"]
     */
    public function test_can_create(?string $column): void
    {
        $params = self::normalParams();
        if (!is_null($column)) {
            $params[$column] = null;
        }
        $contact = Contact::create($params);
        $this->assertNotNull($contact);
    }

    /**
     * @testdox 必要なカラムが不足しているケース
     * @testWith ["first_name"]
     *           ["last_name"]
     *           ["email"]
     *           ["tel"]
     *           ["address"]
     *           ["detail"]
     */
    public function test_cannot_create(string $column): void
    {
        $params = self::normalParams();
        $params[$column] = null;
        $this->assertThrows(function () use ($params) {
            Contact::create($params);
        });
    }

    public static function normalParams(): array
    {
        return [
            'category_id' => Category::create(['content' => fake()->text()])->id,
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
