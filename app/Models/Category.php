<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The guarded attributes.
     * 
     * @var array
     */
    protected $guarded = [];
 
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
            $this->retoolPositions($position);
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
     * @return void
     */
    public function retoolPositions($position)
    {
        $id = $this->id;
        
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
     * Select by ID or create a category.
     * 
     * @param int $category_id
     * @param string $title
     * @return int $id
     */
    public static function selectOrCreate($category_id, $title)
    {
        if($category_id == null){
            $id = self::create([
                'title' => $title,
                'position' => ''
            ])->id;
        }
        else{
            $category = Category::find($category_id);

            if($category == null){
                $id = self::create([
                    'title' => $title,
                    'position' => ''
                ])->id;
            }
            else{
                $id = $category->id;
            }
        }
        return $id;
    }

    /**
     * Get pages for a category.
     * 
     * @return Page[]
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
