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
}
