<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CinemasController;
use App\Http\Controllers\ShowtimesController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\SeatController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('movies',[MovieController::class,'index']);
Route::get('movies/{movie}',[MovieController::class,'show']);
Route::get('cinemas',[CinemasController::class,'index']);
Route::get('cinemas/{cinemas}',[CinemasController::class,'show']);
Route::get('showtimes', [ShowtimesController::class, 'index']);
Route::get('showtimes/{showtime}', [ShowtimesController::class, 'show']);
Route::get('showtimes/{showtime}/reserved-seats', [ShowtimesController::class, 'reservedSeats']);
Route::get('halls',[HallController::class,'index']);
Route::get('halls/{hall}',[HallController::class,'show']);
Route::get('seats',[SeatController::class,'index']);
Route::get('seats/{seat}',[SeatController::class,'show']);
Route::middleware('auth:api')->group(function (){

Route::get('reservations',[ReservationController::class,'index']);
Route::get('reservations/{reservation}',[ReservationController::class,'show']);
Route::post('reservations',[ReservationController::class,'store']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('me', [AuthController::class, 'me']);

});