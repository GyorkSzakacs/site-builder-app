<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Store post data.
     * 
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'title_visibility' => $request->title_visibility,
            'description' => $request->description,
            'content' => $request->content,
            'post_image' => $request->post_image,
            'position' => $request->position,
            'section_id' => $request->section_id
        ]);
    }
}
