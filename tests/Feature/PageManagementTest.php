<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Page;
use Illuminate\Support\Str;

class PageManagementTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     * A user can create a page.
     *
     * @return void
     */
    public function test_user_can_create_a_page()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Page::all());
        $this->assertEquals('fooldal', Page::first()->slug);
    }
}
