<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function store(Request $request)
    {
        Page::create([
            'tittle' => $request->tittle,
            'slug' => $request->slug,
            'tittle_visibility' => $request->tittle_visibility,
            'position' => $request->position,
            'category_id' => $request->category_id
        ]);
    }
}
