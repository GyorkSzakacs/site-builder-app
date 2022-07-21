<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

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
            $position = self::getNextPosition($this->category_id);
        }
        else{
            $this->retoolPositions($position);
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

    /**
     * Get next position.
     * 
     * @param int $category_id
     * @return int $next
     */
    public static function getNextPosition(int $category_id)
    {
        if($category = Category::find($category_id))
        {
            $next = $category->pages->max('position') + 1;
        }
        else
        {
            $next = 1;
        }

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
