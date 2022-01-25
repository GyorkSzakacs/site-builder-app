<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function store(Request $request)
    {

        Category::create([
           'tittle' => $request->tittle,
           'position' => $request->position
        ]);

    }
}
