<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index');
})->name('home');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::middleware('auth:sanctum')->get('/me', function () {
    return auth()->user();
});
