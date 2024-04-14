<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConverterController;

Route::get('/', function () {
    return view('home');
});

Route::get('/text', function () {
    return view('AI.text');
});

Route::get('/image', function () {
    return view('AI.image');
});

Route::get('/pdf-to-word', function () {
    return view('Multimedia.upload');
});

Route::get('/pdf-to-word', [ConverterController::class, 'index']);
Route::post('/convert', [ConverterController::class, 'convert']);
