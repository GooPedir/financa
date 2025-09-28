<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing');
Route::view('/app', 'dashboard');
Route::view('/register', 'register');
