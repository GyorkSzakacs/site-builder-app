<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function store(Request $request)
    {
        $valid_data = $request->validate([
            'tittle' => 'required|max:255',
            'position' => ''
        ]);

        Category::create($valid_data);

    }
}
