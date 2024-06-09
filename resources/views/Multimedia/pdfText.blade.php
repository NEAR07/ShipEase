@extends('layouts.app')
<title>Extract Text</title>
@section('content')
    <style>
        body {
            background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
            background-size: cover;
        }
    </style>
    <div class="container mt-4">
        <div class="row justify-content-center py-6">
            <!-- Guideline box column -->
            <div class="col-md-4">
                <div class="guideline-box">
                    <h4 class="guideline-title text-center">Steps to follow</h4>
                    <ul class="list-unstyled">
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Upload a PDF file by clicking
                            the "Browse" button.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Extract" button to
                            start the text extraction
                            process.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> Wait for the extraction
                            process to complete.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Once completed, the extracted
                            text will be downloaded automatically.</li>
                    </ul>
                </div>
            </div>

            <!-- Main content column -->
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading mt-5">
                        <h2 class="text-center">Extract Text from PDF</h2>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('pdf.to.text.extract') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="inputFile" class="form-label" style="font-weight: bold">Upload PDF File:</label>
                                <input type="file" name="pdf" id="inputFile" class="form-control">
                            </div>

                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-primary">Extract <i
                                        class="fa-solid fa-pen-to-square"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
