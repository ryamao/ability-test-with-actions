<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_to_admin_for_guest_user_redirects_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }
}
