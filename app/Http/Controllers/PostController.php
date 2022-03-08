<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;

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
        $uploadedImage = $request->file('post_image');

        $uploader = new Uploader($uploadedImage, new ImageConstraints());

        $path = $uploader->upload();
        
        Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'title_visibility' => $request->title_visibility,
            'description' => $request->description,
            'content' => $request->content,
            'post_image' => $path,
            'position' => $request->position,
            'section_id' => $request->section_id
        ]);
    }
}
