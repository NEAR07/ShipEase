@extends('layouts.app')
<title>Tools</title>
<style>
    body {
        background-image: url("{{ asset('assets/img/bg5.jpeg') }}");
        background-size: cover;
    }

    .card-link {
        display: block;
        text-decoration: none;
    }

    .card {
        transition: transform 0.2s;
        background-color: #fd7014 !important;
    }

    .card:hover {
        transform: translateY(-3px);
        background-color: #f0f0f0 !important;
    }

    .card-link .card-body h4,
    .card-link .card-body span,
    .card-link .card-body p,
    .card-link .card-body i {
        color: #0b0a0a;
    }

    span {
        margin-top: -10px;
        margin-bottom: 10px;
    }
</style>
@section('content')
    <div class="container mt-6">
        <div class="row py-6">
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/text') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Content Generator</h4>
                                    <span class="badge badge-info">AI Tool</span>
                                    <p>Use AI to write content for you</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/text') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Code Writer</h4>
                                    <span class="badge badge-info">AI Tool</span>
                                    <p>Generate code with AI</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/image') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Image Analysis</h4>
                                    <span class="badge badge-info">AI Tool</span>
                                    <p>Know images better with AI</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/upload') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>PDF to Word Converter</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Convert PDF file to Word</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/pdf-to-text') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>PDF to Text <br>Converter</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Extract text from PDF file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/word-to-pdf') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Word to PDF Converter</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Convert Word file to PDF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/qr-code') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>QR Code Maker</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Generate premium QR Codes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/barcode') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Barcode Maker</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Generate easy-to-use barcodes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/url-shortener') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>URL Shortener</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Shorten your links</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
@endsection
