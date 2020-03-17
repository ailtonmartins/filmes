<?php

use Illuminate\Http\Request;

Route::get('/', function () {
    return response()->json(['message' => 'Filmes API', 'status' => 'Connected']);;
});

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@create');
 
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'UserController@logout'); 
    Route::get('user', 'UserController@show'); 

    /** Movies */
    Route::group(['prefix' => 'movies'], function () {         
        Route::get('/', 'MovieController@index');
        Route::get('/{id}', 'MovieController@show');
        Route::put('/{id}', 'MovieController@update');
        Route::delete('/{id}', 'MovieController@destroy');
        Route::post('/', 'MovieController@store');
    });
});