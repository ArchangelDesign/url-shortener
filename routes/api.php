<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/alive', function () {
    return response('OK', 200);
});
Route::group(['middleware' => 'auth:api_token'], function() {
    Route::get('/website/list', 'App\Http\Controllers\WebsiteController@listWebsites');
    Route::post('/website', 'App\Http\Controllers\WebsiteController@createWebsite');
    Route::delete('/website', 'App\Http\Controllers\WebsiteController@deleteWebsite')
        ->middleware(\App\Http\Middleware\AdminAccess::class);
});
