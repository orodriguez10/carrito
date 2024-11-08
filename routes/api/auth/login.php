<?php

use Illuminate\Support\Facades\Route;

Route::namespace('auth')->group(function () {
    Route::post('login', 'LoginController@login');
});
