<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get next position.
     * 
     * @return int $next
     */
    public static function getNextPosition()
    {
        $next = self::all()->max('position') + 1;
        
        return $next;
    }
    
}
