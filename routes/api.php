<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//


Route::post('login' , 'Api\LoginController@login');
Route::post('register' , 'Api\RegistrationController@register');

Route::middleware('auth:api')->group(function (){
    Route::apiResource('posts' , 'Api\PostController');
    Route::apiResource('categories' , 'Api\CategoryController');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
