<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function (){
Route::get('products/search', [\App\Http\Controllers\ProductController::class, 'search']);
Route::apiResource('products', \App\Http\Controllers\ProductController::class);
//});
