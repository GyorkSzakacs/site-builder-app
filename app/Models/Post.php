<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PositionManagger;
use App\Traits\AttributeSetter;

class Post extends Model
{
    use HasFactory,
        PositionManagger,
        AttributeSetter;

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
}
