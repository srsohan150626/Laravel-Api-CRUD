<?php

Route::group(['middleware' => 'api','prefix' => 'auth','namespace' => 'Api'], function ($router) {
    
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::apiResource('/products','Api\ProductsController')->middleware('api');