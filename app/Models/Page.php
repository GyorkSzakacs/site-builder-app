<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PositionManagger;
use App\Traits\AttributeSetter;

class Page extends Model
{
    use HasFactory,
        PositionManagger,
        AttributeSetter;

    /**
     * The guarded attributes.
     * 
     * @var array
     */
    protected $guarded = [];

    /**
     * Set default value the attributes.
     * 
     * @var array
     */
    protected $attributes = [
        'title_visibility' => true
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'title_visibility' => 'boolean',
    ];

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
     * Get the foreignkey column value.
     * 
     * @return string
     */
    protected function getParentIdColumnValue()
    {
        return $this->category_id;
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
