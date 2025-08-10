<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/halls', 'halls');
Route::view('/showtimes', 'showtimes');
Route::view('/register', 'register')->name('register');

Route::view('/reserve', 'reserve')->name('reserve');            
Route::view('/checkout', 'checkout');
Route::view('/my-reservations', 'my-reservations');
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

// Procesar login (POST) — ya lo sugerimos antes como loginWeb
Route::post('/login', [AuthController::class, 'loginWeb'])
    ->name('login.attempt');

// Logout (POST)
Route::post('/logout', [AuthController::class, 'logoutWeb'])
    ->name('logout')
    ->middleware('auth');
Route::post('/reservations/web', [ReservationController::class, 'storeWeb'])
    ->middleware('auth')
    ->name('reservations.web.store');
Route::get('/web/reservations/json', [ReservationController::class, 'myReservationsJson']);

