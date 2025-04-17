<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function (){

Route::post('logout', [AuthController::class, 'logout']);
Route::post('me', [AuthController::class, 'me']);

});