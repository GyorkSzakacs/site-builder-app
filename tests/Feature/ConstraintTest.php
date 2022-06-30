<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Page;
use App\Models\Section;
use App\Models\Post;
use App\Models\User;

class ConstraintTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a section has benne deleted then it's posts too.
     *
     * @return void
     */
    public function test_posts_of_deleted_section_are_deleted()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);
        
        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post1',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post2',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post3',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 2
        ]);

        $deletedSection = Section::first();

        $this->delete('/section/'.$deletedSection->id);

        $this->assertCount(1, Post::all());
        $this->assertEquals('Post3', Post::first()->title);
    }

    /**
     * Test a page has benne deleted then it's sections and posts too.
     *
     * @return void
     */
    public function test_sections_and_posts_of_deleted_page_are_deleted()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Page1',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció1',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post1',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post2',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post3',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Page2',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció3',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 2
        ]);

        $this->post('/post', [
            'title' => 'Post4',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 3
        ]);

        $deletedPage = Page::first();

        $this->delete('/page/'.$deletedPage->id);

        $this->assertCount(1, Section::all());
        $this->assertEquals('Szekció3', Section::first()->title);
        $this->assertEquals('Post4', Post::first()->title);
    }

    /**
     * Test that pages of a deleted category aren't deleted.
     * 
     * @return void
     */
    public function test_pages_of_deleted_category_are_not_deleted()
    {
        $this->withoutExceptionHandling();

        Category::create([
            'title' => 'Category1',
            'position' => 1
        ]);

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Page1',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Page2',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        Category::create([
            'title' => 'Category2',
            'position' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Page3',
            'title_visibility' => true,
            'category_id' => 2
        ]);

        $deletedCategory = Category::first();

        $deletedCategory->delete();

        $this->assertCount(3, Page::all());
    }
}
