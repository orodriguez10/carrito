<?php

use Illuminate\Support\Facades\Route;

Route::namespace('products')->middleware('auth:api')->group(function () {
    Route::post('productos', 'ProductsController@createOrUpdateProduct');
});

Route::namespace('products')->group(function () {
    Route::get('productos', 'ProductsController@listProducts');
});

