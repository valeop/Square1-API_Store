<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\ProductVariantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    //Authentication
    Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'getProfile']); //get user profile
    Route::post('/register',[AuthController::class, 'register']); //register a user
    Route::post('/login',[AuthController::class, 'login']); //login a user
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); //logout a user

    //products
    Route::prefix('products')->group(function () {
        Route::get('/search', [ProductController::class, 'search']); // search products
        Route::get('/', [ProductController::class, 'index']); // get all products
        Route::get('/{id}', [ProductController::class, 'show']); // get a product by id
        Route::post('/', [ProductController::class, 'store']); // post (store) a product
        Route::put('/{id}', [ProductController::class, 'update']); // put (update) a product
        Route::delete('/{id}', [ProductController::class, 'destroy']); // delete a product

    });

    //orders
    Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [OrderController::class, 'index']); // get all orders
        Route::get('/{id}', [OrderController::class, 'show']); //get an order by id
        Route::post('/create', [OrderController::class, 'store']); //post (store) a new order
    });

    //productVariants
    Route::prefix('variants')->group(function () {
        Route::get('/search', [ProductVariantController::class, 'search']); //search product variants
        Route::get('/', [ProductVariantController::class, 'index']); //get all variants
        Route::get('/{id}', [ProductVariantController::class, 'show']); //get a variant by id
        Route::post('/', [ProductVariantController::class, 'store']); //post (store) a new product variant
        Route::delete('/{id}', [ProductVariantController::class, 'destroy']); //delete a product variant
    });

});
