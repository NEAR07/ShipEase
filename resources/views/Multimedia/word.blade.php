@extends('layouts.app')
<title>Convert Word</title>
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
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Choose a Word file by
                            clicking
                            the "Browse" button. Accepted formats are .doc and .docx</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Convert" button to
                            start the conversion process.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> Wait for the conversion to
                            complete.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Once completed, the
                            converted PDF file will be automatically downloaded.</li>
                    </ul>
                </div>
            </div>

            <!-- Main content column -->
            <div class="col-md-6">
                <div class="panel-heading mt-5">
                    <h2 class="text-center">Convert Word file to PDF</h2>
                </div>
                <form action="{{ route('word.to.pdf.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="inputFile" class="form-label" style="font-weight: bold">Upload Word File:</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".doc, .docx"
                            required>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Convert <i class="fa-solid fa-file"></i></button>
                    </div>
                </form>
                <div id="message" style="font-weight:bold"></div>
            </div>
        </div>
    </div>
@endsection
