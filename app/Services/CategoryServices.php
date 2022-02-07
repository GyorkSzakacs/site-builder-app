<?php
namespace App\Services;

use App\Models\Category;

class CategoryServices
{
    /**
     * Select or create a category for a new page
     * 
     * @param int $category_id
     * @param string $tittle
     * @return int $id
     */
    public function selectOrCreate($category_id, $tittle)
    {
        if($category_id == null){
            $id = Category::create([
                'tittle' => $tittle,
                'position' => ''
            ])->id;
        }
        else{
            $category = Category::all()->find($category_id);

            if($category == null){
                $id = Category::create([
                    'tittle' => $tittle,
                    'position' => ''
                ])->id;
            }
            else{
                $id = $category->id;
            }
        }
        return $id;
    }
}