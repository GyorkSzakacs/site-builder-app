<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'tittle_visibility' => true
    ];

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
     * Set position attribute
     * 
     * @param int $position
     * @return void
     */
    public function setPositionAttribute($position)
    {
        if($position == null){
            $position = self::getNextPosition();
        }

        $this->attributes['position'] = $position;
    }

    /**
     * Set category_id attribute
     * 
     * @param int $category_id
     * @return void
     */
    public function setCategoryIdAttribute($category_id)
    {
        $id = Category::selectOrCreate($category_id, $this->tittle);

        $this->attributes['category_id'] = $id;
    }

    /**
     * Get next position.
     * 
     * @return int $next
     */
    public static function getNextPosition()
    {
        $next = self::max('position') + 1;

        return $next;
    }

    /**
     * Get the category of the page.
     * 
     * @return Category $category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
