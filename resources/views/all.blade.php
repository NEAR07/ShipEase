@extends('layouts.app')
<title>Tools</title>
<style>
    .card-link {
        display: block;
        text-decoration: none;
    }

    .card {
        transition: transform 0.2s;
        background-color: #fff;
        /* Specify initial background color */
    }

    .card:hover {
        transform: translateY(-3px);
        background-color: #f0f0f0;
        /* Change background color on hover */
    }

    /* Ensure text and icon color remain unchanged */
    .card-link .card-body h4,
    .card-link .card-body span,
    .card-link .card-body p,
    .card-link .card-body i {
        color: inherit;
    }

    span {
        margin-top: -10px;
        margin-bottom: 10px;
    }
</style>
@section('content')
    <div class="container" style="margin-top: 8rem;">
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="#" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-pen-to-square fa-3x"></i>
                                </div>
                                <div class="col">
                                    <h4>Compress PDF</h4>
                                    <span class="badge badge-info">Pdf Tools</span>
                                    <p>Lessen the file size of a PDF file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="#" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-file fa-3x"></i>
                                </div>
                                <div class="col">
                                    <h4>Compress PDF</h4>
                                    <span class="badge badge-info">Pdf Tools</span>
                                    <p>Lessen the file size of a PDF file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="#" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-file fa-3x"></i>
                                </div>
                                <div class="col">
                                    <h4>Compress PDF</h4>
                                    <span class="badge badge-info">Pdf Tools</span>
                                    <p>Lessen the file size of a PDF file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="#" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-file fa-3x"></i>
                                </div>
                                <div class="col">
                                    <h4>Compress PDF</h4>
                                    <span class="badge badge-info">Pdf Tools</span>
                                    <p>Lessen the file size of a PDF file</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
@endsection
