<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register','App\Http\Controllers\AuthController@register');
Route::post('login','App\Http\Controllers\AuthController@login');

Route::get('invalid',function(){
	 return response()->json(['message'=>'Access token not matched'],422);
})->name('invalid');


Route::middleware('auth:sanctum')->group(function () {

	Route::post('update','App\Http\Controllers\AuthController@update');

	Route::post('spaces','App\Http\Controllers\SpaceController@index');
	Route::post('spaces-of-user','App\Http\Controllers\SpaceController@index2');
	Route::post('spaces-store','App\Http\Controllers\SpaceController@store');
	Route::post('spaces-update/{id}','App\Http\Controllers\SpaceController@update');
	Route::post('spaces-delete/{id}','App\Http\Controllers\SpaceController@destroy');
	
});
