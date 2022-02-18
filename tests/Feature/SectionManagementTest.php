<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Section;

class SectionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Return section input data.
     * 
     * @return array $input
     */
    protected function input()
    {
        return [
            'title' => 'Hírek',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ];
    }
    
    /**
     * Test a section can be created.
     *
     * @return void
     */
    public function test_a_section_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/section', $this->input());

        $response->assertOk();
        $this->assertCount(1, Section::all());
        $this->assertEquals('Hírek', Section::first()->title);
    }

    /**
     * Test a section can be updated.
     *
     * @return void
     */
    public function test_a_section_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $this->post('/section', $this->input());

        $section = Section::first();

        $response = $this->patch('/section/'.$section->id, [
            'title' => 'Érdekességek',
            'title_visibility' => false,
            'position' => 1,
            'page_id' => 1
        ]);

        $response->assertOk();
        $this->assertCount(1, Section::all());
        $this->assertEquals('Érdekességek', Section::first()->title);
        $this->assertEquals('erdekessegek', Section::first()->slug);
        $this->assertEquals(0, Section::first()->title_visibility);
    }

     /**
     * Test a section can be deleted.
     *
     * @return void
     */
    public function test_a_section_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $this->post('/section', $this->input());

        $section = Section::first();

        $response = $this->delete('/section/'.$section->id);

        $response->assertOk();
        $this->assertCount(0, Section::all());
    }

    /**
     * Test validation of input data.
     * 
     * @return void
     */
    public function test_section_input_data_validation()
    {
        $response = $this->post('/section', [
            'title' => '',
            'title_visibility' => 'data',
            'position' => 'data',
            'page_id' => 'two'
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionHasErrors('position');
        $response->assertSessionHasErrors('page_id');
    }

    /**
     * Test get next section position.
     * 
     * @return void
     */
    public function test_get_next_section_position()
    {
        $next = Section::getNextPosition();

        $this->assertEquals(1, $next);
    }

    /**
     * Test set next position.
     * 
     * @return void
     */
    public function test_set_next_section_position()
    {
        $this->post('/section', $this->input());
        
        $this->post('/section', [
            'title' => 'Érdekességek',
            'title_visibility' => true,
            'position' => Section::getNextPosition(),
            'page_id' => 1
        ]);

        $this->assertEquals(2, Section::find(2)->position);
    }

    /**
     * Test set default title visibility
     * 
     * @return void
     */
    public function test_set_default_section_title_visibility()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/section', [
            'title' => 'Hírek',
            'position' => 1,
            'page_id' => 1
        ]);

        $this->assertEquals(1, Section::first()->title_visibility);
    }

    /**
     * Test set next position if position attribute is null.
     * 
     * @return void
     */
    public function test_section_position_is_null()
    {
        Section::create([
            'title' => 'Hírek',
            'slug' => '',
            'title_visibility' => true,
            'position' => '',
            'page_id' => 1
        ]);

        $this->assertEquals(1, Section::first()->position);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_section_positions()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/section', [
            'title' => 'Szkció1',
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

        $this->post('/section', [
            'title' => 'Szekció3',
            'title_visibility' => true,
            'position' => 3,
            'page_id' => 1
        ]);

        $occupied = Section::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Section::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->post('/section', [
            'title' => 'Szekció4',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        $first = Section::first();
        $third = Section::find(2);
        $forth = Section::find(3);
        $second = Section::find(4);

        $this->assertCount(4, Section::all());
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        $this->assertEquals(3, $third->position);
        $this->assertEquals(4, $forth->position);
    }
}
