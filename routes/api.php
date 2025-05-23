<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TestController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

 // Test
    Route::post('/test', [TestController::class, 'store']);
    Route::get('/test/{id}', [TestController::class, 'show']);
    Route::put('/test/{id}', [TestController::class, 'update']);
    Route::delete('/test/{id}', [TestController::class, 'destroy']);

// Protected routes (using Passport's auth:api middleware)
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);

    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Comments
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    // Categories 
    Route::middleware('checkrole:admin')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });

    Route::post('/posts/{postId}/assign-categories', [CategoryController::class, 'assignCategoriesToPost']);
    Route::get('/categories/{categoryId}/posts', [CategoryController::class, 'getPostsByCategory']);

    // Likes
    Route::post('/posts/{id}/like', [LikeController::class, 'toggleLike']);
    Route::get('/posts/{id}/likes', [LikeController::class, 'totalLikes']);

});
