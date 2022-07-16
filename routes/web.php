<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

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

Route::get('/', [PostController::class, 'index']);

Route::post('/ckupload', [PostController::class, 'upload'])->name('ckupload')->middleware(['auth']);

Route::get('/post/create', [PostController::class, 'create'])->middleware(['auth']);
Route::get('/post/tags', [PostController::class, 'getTags']);
Route::get('/post/tags/{tag}', [PostController::class, 'getTags']);
Route::get('/post/{post}/edit', [PostController::class, 'edit'])->middleware(['auth']);
Route::get('/post/{post}/settings', [PostController::class, 'settings'])->middleware(['auth']);
Route::post('/post/{post}/update', [PostController::class, 'update'])->middleware(['auth']);
Route::patch('/post/{post}/publish', [PostController::class, 'publish'])->middleware(['auth']);
Route::post('/post/{post}/like', [PostController::class, 'like'])->middleware(['auth']);
Route::post('/post/{post}/comment', [CommentController::class, 'store'])->middleware(['auth']); // send comment
Route::delete('/post/{post}', [PostController::class, 'destroy'])->middleware(['auth']);
Route::get('/post/{id}', [PostController::class, 'show']);
Route::post('/post/store', [PostController::class, 'store'])->middleware(['auth']);

Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->middleware(['auth']);
Route::post('/comment/{comment}/like', [CommentController::class, 'like'])->middleware(['auth']);


Route::get('/user/login', [UserController::class, 'login'])->name('login');
Route::get('/user/create', [UserController::class, 'register'])->name('register');
Route::post('/user/store', [UserController::class, 'store']);
Route::post('/user/auth', [UserController::class, 'auth']);
Route::post('/user/logout', [UserController::class, 'logout'])->middleware(['auth']);

Route::get('/user/{username}', [UserController::class, 'show']);

Route::patch('/user/{user}/name', [UserController::class, 'updateName'])->middleware(['auth']);
Route::patch('/user/{user}/picture', [UserController::class, 'updatePicture'])->middleware(['auth']);

Route::get('/user/{user}/followers', [UserController::class, 'followers']);
Route::get('/user/{user}/follows', [UserController::class, 'follows']);
Route::post('/user/{user}/follow', [UserController::class, 'follow'])->middleware(['auth']);
Route::delete('/user/{user}/follow', [UserController::class, 'unfollow'])->middleware(['auth']);