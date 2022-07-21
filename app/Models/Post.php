<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

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
     * Set position attribute
     * 
     * @param int $position
     * @return void
     */
    public function setPositionAttribute($position)
    {
        if($position == null){
            $position = self::getNextPosition($this->section_id);
        }
        else{
            $this->retoolPositions($position);
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
     * Get the foreignkey column value.
     * 
     * @return string
     */
    protected function getParentIdColumnValue()
    {
        return $this->section_id;
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

    /**
     * Get next position.
     * 
     * @param int $section_id
     * @return int $next
     */
    public static function getNextPosition(int $section_id)
    {
        $section = Section::find($section_id);
        
        $next = $section->posts->max('position') + 1;
        
        return $next;
    }

    /**
     * Retool positions if the requested has been already occupied.
     * 
     * @param int $position
     * @return void
     */
    public function retoolPositions($position)
    {
        $id = $this->id;
        $parentId = $this->getParentIdColumnValue();

        $parentIdColumnName = self::getParentIdColumnName();

        $occupied = self::where([
                            [$parentIdColumnName, $parentId],
                            ['position', $position]
                        ])->first();
        if($occupied != null && $occupied->id != $id)
        {
            $items = self::where([
                            [$parentIdColumnName, $parentId],
                            ['position', '>=', $position]
                        ])->get();

            foreach($items as $item){
                $newPosition = $item->position + 1;

                $item->update([
                    'position' => $newPosition
                ]);
            }
        }
    }

}
