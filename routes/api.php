<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/registration', [AuthController::class, 'registration']);

Route::middleware(['auth:api'])->group(function () {

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts/store', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'edit']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/update/{id}', [PostController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});