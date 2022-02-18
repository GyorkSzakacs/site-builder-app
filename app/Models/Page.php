<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
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
            self::retoolPositions($position, $this->id);
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
     * @return void
     */
    public static function retoolPositions($position, $id)
    {
        $occupied = self::where('position', $position)->first();

        if($occupied != null && $occupied->id != $id){
            $items = self::where('position', '>=', $position)->get();

            foreach($items as $item){
                $newPosition = $item->position + 1;

                $item->update([
                    'position' => $newPosition
                ]);
            }
        }
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
