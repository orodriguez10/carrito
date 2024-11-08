<?php

use Illuminate\Support\Facades\Route;

Route::namespace('car')->middleware('auth:api')->group(function () {
    Route::post('carrito', 'ShoppingCarController@addToCart');
    Route::get('carrito', 'ShoppingCarController@viewCart');
});


