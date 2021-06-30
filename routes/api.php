<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/alive', function () {
    return response('OK', 200);
});
Route::get('/website/list', 'App\Http\Controllers\WebsiteController@listWebsites');
Route::post('/website', 'App\Http\Controllers\WebsiteController@createWebsite');
Route::delete('/website', 'App\Http\Controllers\WebsiteController@deleteWebsite');
