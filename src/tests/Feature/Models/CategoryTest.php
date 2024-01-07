<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @testdox 作成可能なケース
     * @group model
     * @testWith ["foo"]
     */
    public function test_can_create(string $content): void
    {
        $category = Category::create(compact('content'));
        $this->assertNotNull($category);
    }

    /**
     * @testdox 必要なカラムが不足しているケース
     * @group model
     */
    public function test_cannot_create_when_required_column_is_missing(): void
    {
        $this->assertThrows(function () {
            Category::create([]);
        });
    }
}
