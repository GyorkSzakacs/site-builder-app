<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    /**
     * Create a new category.
     * 
     * @param CategoryRequest $request
     * @return void
     */
    public function store(CategoryRequest $request)
    {

        $valid_data = $request->validated();

        Category::create($valid_data);

        return redirect('/dashboard');

    }

    /**
     * Update the selected category.
     * 
     * @param CategoryRequest $request
     * @param Category $category
     * @return void
     */
    public function update(CategoryRequest $request, Category $category)
    {
        
        $valid_data = $request->validated();

        $category->update($valid_data);

        return redirect('/dashboard');

    }

    /**
     * Delete the selected category.
     * 
     * @param Category $category
     */
    public function destroy(Category $category)
    {

        $category->delete();

        return redirect('/dashboard');

    }
    
}
