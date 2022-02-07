<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Set slug attribute.
     * 
     * @param string $slug
     * @return void
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = Str::slug($this->tittle, '-');
    }

    /**
     * Set category_id attribute
     * 
     * @param int $category_id
     * @return void
     */
    public function setCategoryIdAttribute($category_id)
    {
        if($category_id == null){
            $id = Category::create([
                'tittle' => $this->tittle,
                'position' => 1
            ])->id;
        }
        else{
            $category = Category::all()->find($category_id);

            if($category == null){
                $id = Category::create([
                    'tittle' => $this->tittle,
                    'position' => 1
                ])->id;
            }
            else{
                $id = $category->id;
            }
        }

        $this->attributes['category_id'] = $id;
    }
}
