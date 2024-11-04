<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/registration', [AuthController::class, 'registration']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    
    Route::post('/posts/store', [PostController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});