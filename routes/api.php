<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\ProductVariantController;
use App\Http\Controllers\Api\v1\ShoppingCartController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    //Authentication
    Route::post('/register',[AuthController::class, 'register']); //register a user
    Route::post('/login',[AuthController::class, 'login']); //login a user
    Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'getProfile']); //get user profile
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

    //shoppingCart
    Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ShoppingCartController::class, 'index']); //get shopping cart content per user
        Route::post('/add', [ShoppingCartController::class, 'store']); //add a new item to shopping cart
        Route::put('/update/{id}', [ShoppingCartController::class, 'update']); //update an item in shopping cart
        Route::delete('/remove/{id}', [ShoppingCartController::class, 'destroy']); //delete an item from shopping cart
    });

});
