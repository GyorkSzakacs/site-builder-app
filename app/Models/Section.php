<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
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
            $position = self::getNextPosition($this->page_id);
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

    
    /**
     * Get next position.
     * 
     * @param int $page_id
     * @return int $next
     */
    public static function getNextPosition(int $page_id)
    {
        $page = Page::find($page_id);
        
        $next = $page->sections->max('position') + 1;
        
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
