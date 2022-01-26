<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function store(CategoryRequest $request)
    {

        $valid_data = $request->validated();

        Category::create($valid_data);

    }

    public function update(CategoryRequest $request, Category $category)
    {
        
        $valid_data = $request->validated();

        $category->update($valid_data);

    }
    
}
