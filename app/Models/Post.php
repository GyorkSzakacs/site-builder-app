<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\PositionManagger;

class Post extends Model
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
            self::retoolPositions($position, $this->id, $this->section_id);
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
        return 'section_id';
    }

    /**
     * Get the section of the post.
     * 
     * @return Section
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
