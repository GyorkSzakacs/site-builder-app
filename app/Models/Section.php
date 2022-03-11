<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\PositionManagger;

class Section extends Model
{
    use HasFactory;
    use PositionManagger;

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
     * Set position attribute.
     * 
     * @param int  $position
     * @return void
     */
    public function setPositionAttribute($position)
    {
        if($position == null){
            $position = self::getNextPosition();
        }
        else{
            self::retoolPositions($position, $this->id, $this->page_id);
        }

        $this->attributes['position'] = $position;
    }

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
