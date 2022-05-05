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
        if(!$request->user()->hasManagerAccess())
        {
            return abort(403);
        }
        
        Category::create($this->getValidData($request));

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
        if(!$request->user()->hasManagerAccess())
        {
            return abort(403);
        }

        $category->update($this->getValidData($request));

        return redirect('/dashboard');
    }

    /**
     * Delete the selected category.
     * 
     * @param Request $request
     * @param Category $category
     */
    public function destroy(Request $request, Category $category)
    {
        if(!$request->user()->hasManagerAccess())
        {
            return abort(403);
        }

        $category->delete();

        return redirect('/dashboard');
    }
    
    /**
     * Get valid input data.
     * 
     * @param CategoryRequest $request
     * @return array
     */
    protected function getValidData(CategoryRequest $request)
    {
        return $request->validated();
    }
}
