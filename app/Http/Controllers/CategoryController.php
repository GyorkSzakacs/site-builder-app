<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    /**
     * Constructor for CategoryController class.
     */
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Get the screen with the form for creation of a category.
     * 
     * @return View
     */
    public function create()
    {
        $next = Category::getNextPosition();

        return view('category.create', ['next' => $next]);
    }
    
    /**
     * Create a new category.
     * 
     * @param CategoryRequest $request
     * @return void
     */
    public function store(CategoryRequest $request)
    {
        Category::create($this->getValidData($request));

        return redirect('/dashboard');
    }

    /**
     * Get the scrren with the form for updating a category.
     * 
     * @param Request $request
     * @param Category $category
     * @return View
     */
    public function edit(Request $request, Category $category)
    {
        $max = Category::max('position');

        return view('category.update', [
                                        'category' => $category,
                                        'max' => $max
                                    ]);
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
