<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordToPDFController;
use App\Http\Controllers\pdfTextController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\URLShortenerController;

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

Route::get('/form/{form}', [FormController::class, 'showForm'])->name('showForm');

//Barcode Routes
Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');
Route::post('/barcode/generate', [BarcodeController::class, 'generate'])->name('barcode.generate');
Route::get('/download/barcodes/{filename}', [BarcodeController::class, 'downloadBarcode'])->name('barcode.download');

//QRcode Routes
Route::get('/showForm/{form}', 'QRCodeController@index')->name('showForm');
Route::post('/generate', [QRCodeController::class, 'generate'])->name('qrcode.generate');
Route::get('/download/qrcodes/{filename}', [QRCodeController::class, 'downloadFile'])->name('qrcode.download.file');

// Link Shortener Routes
Route::get('/url-shortener', function () {
    return view('Multimedia.forms.shortened_url');
});
Route::post('/shorten-url', [URLShortenerController::class, 'shorten'])->name('url.shorten');
Route::get('/cra.ft/{shortenedURL}', [URLShortenerController::class, 'redirect']);
