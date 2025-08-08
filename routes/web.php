<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/halls', 'halls');
Route::view('/showtimes', 'showtimes');
Route::view('/register', 'register')->name('register');
Route::view('/login', 'login')->name('login');
Route::view('/reserve', 'reserve')->name('reserve');            


