<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

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
     * Retool positions if the requested has been already occupied.
     * 
     * @param int $position
     * @param int $id
     * @param int $parentId
     * @return void
     */
    public static function retoolPositions($position, $id, $parentId)
    {
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
