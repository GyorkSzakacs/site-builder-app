<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get next category position test.
     *
     * @return void
     */
    public function test_get_next_category_position()
    {
        
        Category::create([
            'tittle' => 'Valami',
            'position' => 1
        ]);
        
        $next = Category::getNextPosition();

        $this->assertEquals(2, $next);
    }
}
