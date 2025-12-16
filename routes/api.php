<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

// Default user route (commented out)
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| These routes are publicly accessible for registering and logging in users.
*/
Route::post('register',[RegisterController::class,'register']); // Register a new user
Route::post('login',[RegisterController::class,'login']);       // Login and get Sanctum API token

/*
|--------------------------------------------------------------------------
| Protected Product Routes
|--------------------------------------------------------------------------
| These routes are protected using Sanctum middleware.
| Only authenticated users with a valid API token can access them.
*/
Route::middleware('auth:sanctum')->group(function () {

    // List all products (GET)
    Route::get('/listProducts', [ProductController::class, 'listProducts']);

    // Add new product (POST)
    Route::post('/addProduct', [ProductController::class, 'createProduct']);

    // Show single product details (GET)
    Route::get('/showProduct/{id}', [ProductController::class, 'showProduct']);

    // Update product details (POST)
    Route::post('/updateProduct/{id}', [ProductController::class, 'updateProduct']);

    // Delete product (Soft Delete) (POST)
    Route::post('/deleteProduct/{id}', [ProductController::class, 'deleteProduct']);
});
