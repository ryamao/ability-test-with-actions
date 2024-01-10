<?php

namespace Tests\Feature\View;

use App\Models\Category;
use App\Models\Contact;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group admin
     */
    public function test_it_can_render(): void
    {
        app(DatabaseSeeder::class)->run();

        $view = $this->view('admin', [
            'categories' => Category::all(),
            'contacts' => Contact::paginate(10),
            'keywordsString' => '',
            'gender' => null,
            'categoryId' => null,
            'datetime' => null,
        ]);

        foreach (Category::all() as $category) {
            $view->assertSee('value="' . $category->id . '"', escape: false);
            $view->assertSeeText($category->content);
        }

        foreach (Contact::limit(10) as $contact) {
            $view->assertSeeText($contact->last_name);
            $view->assertSeeText($contact->first_name);
            $view->assertSeeText($contact->genderName);
            $view->assertSeeText($contact->email);
            $view->assertSeeText($contact->tel);
            $view->assertSeeText($contact->address);
            $view->assertSeeText($contact->building);
            $view->assertSeeText($contact->detail);
        }

        foreach (Contact::offset(11) as $contact) {
            $view->assertDontSeeText($contact->detail);
        }
    }
}
