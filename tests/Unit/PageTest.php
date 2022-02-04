<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Str;

class PageTest extends TestCase
{
    /**
     * Slug test.
     *
     * @return void
     */
    public function test_slug()
    {
        $slug = Str::slug('A mi főoldalunk', '-');

        $this->assertEquals('a-mi-fooldalunk', $slug);
    }
}
