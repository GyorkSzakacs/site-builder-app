<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\PositionManagger;

class Page extends Model
{
    use HasFactory;
    use PositionManagger;

    protected $guarded = [];

    protected $attributes = [
        'title_visibility' => true
    ];

    /**
     * Set slug attribute.
     * 
     * @param string $slug
     * @return void
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = Str::slug($this->title, '-');
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
        else{
            self::retoolPositions($position, $this->id, $this->category_id);
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
        $id = Category::selectOrCreate($category_id, $this->title);

        $this->attributes['category_id'] = $id;
    }

    /**
     * Get the foreignkey column name.
     * 
     * @return string
     */
    protected static function getParentIdColumnName()
    {
        return 'category_id';
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

    /**
     * Get all sections for a page.
     * 
     * @return Section[]
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
