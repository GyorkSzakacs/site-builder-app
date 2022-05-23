<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Page;
use App\Models\Section;
use App\Models\Post;

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

Route::get('/', [PageController::class, 'index']);

//Category management routes
Route::post('/category', [CategoryController::class, 'store'])->can('create', Category::class);
Route::patch('/category/{category}', [CategoryController::class, 'update'])->can('update', 'category');
Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->can('delete', 'category');

//Page management routes
Route::post('/page', [PageController::class, 'store'])->can('create', Page::class);
Route::patch('/page/{page}', [PageController::class, 'update'])->can('update', 'page');
Route::delete('/page/{page}', [PageController::class, 'destroy'])->can('delete', 'page');

//Section management routes
Route::post('/section', [SectionController::class, 'store'])->can('create', Section::class);
Route::patch('/section/{section}', [SectionController::class, 'update'])->can('update', 'section');
Route::delete('/section/{section}', [SectionController::class, 'destroy'])->can('delete', 'section');

//Post management routes
Route::post('/post', [PostController::class, 'store'])->can('create', Post::class);
Route::patch('/post/{post}', [PostController::class, 'update'])->can('update', 'post');
Route::delete('/post/{post}', [PostController::class, 'destroy'])->can('delete', 'post');

//Image management routes
Route::post('/image', [ImageController::class, 'store'])->can('image-upload');
Route::post('/image/{image}', [ImageController::class, 'download'])->can('image-download');
Route::delete('/image/{image}', [ImageController::class, 'destroy'])->can('image-delete');

//User management routs
Route::patch('/account/{user}', [UserController::class, 'update'])->can('update', 'user');
Route::patch('/account-access/{user}', [UserController::class, 'updateAccess'])->can('updateAccess', 'user');
Route::delete('/account/{user}', [UserController::class, 'destroy'])->can('delete', 'user');
Route::post('/update-password/{user}', [UserController::class, 'updatePassword'])->can('updatePassword', 'user');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
