<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PositionManagger;
use App\Traits\AttributeSetter;

class Section extends Model
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
     * Set default values of attributes.
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
     * Get the foreignkey column name.
     * 
     * @return string
     */
    protected static function getParentIdColumnName()
    {
        return 'page_id';
    }

    /**
     * Get the foreignkey column value.
     * 
     * @return string
     */
    protected function getParentIdColumnValue()
    {
        return $this->page_id;
    }

    /**
     * Get the page of the section.
     * 
     * @return Page $page
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get all posts for a section.
     * 
     * @return Post[]
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
