<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordToPDFController;
use App\Http\Controllers\pdfTextController;


Route::get('/', function () {
    return view('home');
});

Route::get('/text', function () {
    return view('AI.text');
});

Route::get('/image', function () {
    return view('AI.image');
});

//Word2Pdf
Route::get('word-to-pdf', [WordToPDFController::class, 'index']);
Route::post('word-to-pdf', [WordToPDFController::class, 'store'])->name('word.to.pdf.store');

//Pdf2Text
Route::get('/pdf-to-text', function () {
    return view('Multimedia.pdfText');
});
Route::get('/pdf-to-text', [pdfTextController::class, 'index']);
Route::post('/extract', [pdfTextController::class, 'extract'])->name('pdf.to.text.extract');
