<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [RegisterController::class, 'logout']);
    Route::post('logout-all', [RegisterController::class, 'logoutAllDevices']);
    Route::post('change-password', [RegisterController::class, 'changePassword']);
    Route::post('update-profile', [RegisterController::class, 'updateProfile']);
    Route::post('update-avatar', [RegisterController::class, 'updateAvatar']);

    Route::get('/listProducts', [ProductController::class, 'listProducts']);
    Route::post('/addProduct', [ProductController::class, 'createProduct']);
    Route::get('/showProduct/{id}', [ProductController::class, 'showProduct']);
    Route::post('/updateProduct/{id}', [ProductController::class, 'updateProduct']);
    Route::post('/deleteProduct/{id}', [ProductController::class, 'deleteProduct']);
    
});