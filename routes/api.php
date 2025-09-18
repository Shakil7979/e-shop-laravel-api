<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserAuthController;

// admin auth route 
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']); 

// product route 
Route::apiResource('products', ProductController::class);

// order route 
Route::apiResource('orders', OrderController::class);

// User Authentication REST API
Route::post('/users/register', [UserAuthController::class, 'store']); // POST /users/register
Route::post('/users/login', [UserAuthController::class, 'login']);  

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [UserAuthController::class, 'me']);
    Route::post('/logout', [UserAuthController::class, 'logout']);
});





 