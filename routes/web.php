<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordToPDFController;
use App\Http\Controllers\pdfTextController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\URLShortenerController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PdfNumberingController;
use App\Http\Controllers\PartlistConverterController;
use App\Http\Controllers\PartnameConverterController;
use App\Http\Controllers\ResumeDownloadController;
use App\Http\Controllers\CompareDownloadController;
use App\Http\Controllers\AutolispDownloadController;
use App\Http\Controllers\DxfProcessorController;


//Home
Route::get('/', function () {
    return view('home');
});

//PDF Merge
Route::post('/merge-pdfs', [PdfController::class, 'merge'])->name('merge.pdfs');
Route::get('/merge_pdf', function () {
    return view('Multimedia.merge_pdf');
});

// PDF Numbering
Route::post('/number-pdf', [PdfNumberingController::class, 'number'])->name('number.pdf');
Route::get('/number-pdf', function () {
    return view('Multimedia.number');
});

// partlist
Route::post('/partlist-converter', [PartlistConverterController::class, 'convert'])->name('partlist.convert');
Route::get('/partlist-converter', function () {
    return view('Multimedia.partlistConverter');
});

// Route::post('/partnameConverter', [PartnameConverterController::class, 'process'])->name('partnameConverter.process');
// Route::post('/partnameConverter/zip', [PartnameConverterController::class, 'downloadZip'])->name('partnameConverter.zip');
// Route::get('/partnameConverter', function () {
//     return view('Multimedia.partnameConverter');
// });
Route::get('/partnameConverter', [PartnameConverterController::class, 'index'])->name('partnameConverter.form');
Route::get('/partnameConverter/download', [PartnameConverterController::class, 'downloadApp'])->name('partnameConverter.download');

Route::get('/download', [ResumeDownloadController::class, 'showDownloadPage'])->name('download.page');
Route::get('/download/resume', [ResumeDownloadController::class, 'downloadResume'])->name('resume.download');

Route::get('/compare-download', [CompareDownloadController::class, 'show'])->name('compare.page');
Route::get('/compare-download/file', [CompareDownloadController::class, 'downloadCompare'])->name('compare.download');

Route::get('/autolisp-download', [AutolispDownloadController::class, 'show'])->name('autolisp.show');
Route::post('/autolisp-download', [AutolispDownloadController::class, 'downloadAutolisp'])->name('autolisp.download');

Route::get('/process-dxf', [DxfProcessorController::class, 'index'])->name('process.dxf.form');
Route::get('/download-app', [DxfProcessorController::class, 'downloadApp'])->name('download.app');

//AI module
Route::get('/text', function () {
    return view('AI.text');
});
Route::get('/image', function () {
    return view('AI.image');
});

//All tools
Route::get('/all-tools', function () {
    return view('all');
});

//Word2Pdf
Route::get('/word-to-pdf', function () {
    return view('Multimedia.word');
});
Route::post('word-to-pdf', [WordToPDFController::class, 'store'])->name('word.to.pdf.store');

//Pdf2Text
Route::get('/pdf-to-text', function () {
    return view('Multimedia.pdfText');
});
Route::post('/extract', [pdfTextController::class, 'extract'])->name('pdf.to.text.extract');

//QR Maker
Route::get('/qr-code', function () {
    return view('Multimedia.forms.QRcode');
});
Route::get('/generate-qr-code', [QRCodeController::class, 'generateQRCode']);

//Barcode
Route::get('/barcode', function () {
    return view('Multimedia.forms.barcode');
});
Route::get('/generate-barcode', [BarcodeController::class, 'generateBarcode']);

// Link Shortener
Route::get('/url-shortener', function () {
    return view('Multimedia.forms.shortened_url');
});
Route::post('/shorten-url', [URLShortenerController::class, 'shorten'])->name('url.shorten');
Route::get('/cra.ft/{shortenedURL}', [URLShortenerController::class, 'redirect']);

//Pdf-to-Docx
Route::get('/upload', [ConversionController::class, 'showUploadForm']);
Route::post('/convert', [ConversionController::class, 'convertPdfToDocx']);
