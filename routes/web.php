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
use App\Models\User;

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
Route::middleware('can:create,App\Models\Category')->group(function(){
    Route::get('/create-category', [CategoryController::class, 'create'])
        ->name('create-category');
    Route::post('/category', [CategoryController::class, 'store']);
});

Route::middleware('can:update,category')->group(function(){
    Route::get('/update-category/{category}', [CategoryController::class, 'edit']);
    Route::patch('/category/{category}', [CategoryController::class, 'update']);
});

Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->can('delete', 'category');

//Page management routes
Route::middleware('can:create,App\Models\Page')->group(function(){
    Route::get('/create-page', [PageController::class, 'create'])
        ->name('create-page');
    Route::post('/page', [PageController::class, 'store']);
});

Route::middleware('can:update,page')->group(function(){
    Route::get('/update-page/{page}', [PageController::class, 'edit']);
    Route::patch('/page/{page}', [PageController::class, 'update']);
});

Route::delete('/page/{page}', [PageController::class, 'destroy'])->can('delete', 'page');

//Section management routes
Route::middleware('can:create,App\Models\Section')->group(function(){
    Route::get('/{id}/create-section', [SectionController::class, 'create'])
        ->name('create-section');
    Route::post('/section', [SectionController::class, 'store']);
});

Route::middleware('can:update,section')->group(function(){
    Route::get('/update-section/{section}', [SectionController::class, 'edit']);
    Route::patch('/section/{section}', [SectionController::class, 'update']);
});

Route::delete('/section/{section}', [SectionController::class, 'destroy'])->can('delete', 'section');

//Post management routes
Route::middleware('can:create,App\Models\Post')->group(function(){
    Route::get('/{id}/create-post', [PostController::class, 'create'])
            ->name('create-post');
    Route::post('/post', [PostController::class, 'store']);
});

Route::middleware('can:update,post')->group(function(){
    Route::get('/update-post/{post}', [PostController::class, 'edit']);
    Route::patch('/post/{post}', [PostController::class, 'update']);
});

Route::delete('/post/{post}', [PostController::class, 'destroy'])->can('delete', 'post');

//Image management routes
Route::post('/image', [ImageController::class, 'store'])
        ->name('upload-image')
        ->can('image-upload');
        
Route::post('/image/{image}', [ImageController::class, 'download'])->can('image-download');
Route::delete('/image/{image}', [ImageController::class, 'destroy'])->can('image-delete');

//User management routs
Route::patch('/account/{user}', [UserController::class, 'update'])->can('update', 'user');

Route::middleware('can:updateAccess,user')->group(function(){
    Route::get('/account-access/{user}', [UserController::class, 'editAccess']);
    Route::patch('/account-access/{user}', [UserController::class, 'updateAccess']);
});

Route::delete('/account/{user}', [UserController::class, 'destroy'])->can('delete', 'user');
Route::post('/update-password/{user}', [UserController::class, 'updatePassword'])->can('updatePassword', 'user');

Route::get('/dashboard', function () {
    $users = User::all();

    $categories = Category::orderBy('position', 'asc')
                            ->get();

    $pages = Page::all();                        

    return view('dashboard', [
                                'users' => $users,
                                'categories' => $categories,
                                'pages' => $pages
                            ]);
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

//Get post show screen.
Route::get('/{pageSlug}/{sectionSlug}/{postSlug}', [PostController::class, 'show']);

//Get page content with page slug. This should be the last route in the list.
Route::get('/{slug}', [PageController::class, 'show']);
