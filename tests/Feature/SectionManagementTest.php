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
}
