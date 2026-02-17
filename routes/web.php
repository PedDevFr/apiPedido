<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('index'); //Vista principal
Route::view('/about', 'about')->name('about'); //Vista about
Route::view('/services', 'services')->name('services'); //Vista services
Route::view('/contact', 'contact')->name('contact'); //Vista contact