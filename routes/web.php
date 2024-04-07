<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/text', function () {
    return view('AI.text');
});

Route::get('/image', function () {
    return view('AI.image');
});
