<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Category management routes
Route::post('/category', [CategoryController::class, 'store']);
Route::patch('/category/{category}', [CategoryController::class, 'update']);
Route::delete('/category/{category}', [CategoryController::class, 'destroy']);

//Page menegement routes
Route::post('/page', [PageController::class, 'store']);
Route::patch('/page/{page}', [PageController::class, 'update']);
Route::delete('/page/{page}', [PageController::class, 'destroy']);

//Section management routes
Route::post('/section', [SectionController::class, 'store']);
Route::patch('/section/{section}', [SectionController::class, 'update']);
Route::delete('/section/{section}', [SectionController::class, 'destroy']);

//Post management routes
Route::post('/post', [PostController::class, 'store']);
Route::patch('/post/{post}', [PostController::class, 'update']);
Route::delete('/post/{post}', [PostController::class, 'destroy']);

//Image management routes
Route::post('/image', [ImageController::class, 'store']);