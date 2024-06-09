@extends('layouts.app')
<title>Convert PDF</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Choose a PDF file by clicking
                            the "Browse" button.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Convert" button to
                            start the conversion process.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> Wait for the conversion to
                            complete.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Once completed, the
                            converted Word file will be automatically downloaded.</li>
                    </ul>
                </div>
            </div>

            <!-- Main content column -->
            <div class="col-md-6">
                <div class="panel-heading mt-5">
                    <h2 class="text-center">Convert PDF file to Word</h2>
                </div>
                <form id="uploadForm" enctype="multipart/form-data" method="POST" action="/convert">
                    @csrf
                    <div class="mb-3">
                        <label for="inputFile" class="form-label" style="font-weight: bold">Upload PDF File:</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf" required>
                    </div>

                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Convert <i class="fa-solid fa-file"></i></button>
                    </div>
                </form>
                <div id="message" style="font-weight:bold"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('/convert', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = document.getElementById('file').files[0].name.replace('.pdf', '.docx');
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.getElementById('message').innerText =
                        'File converted successfully and downloaded.';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').innerText = 'Error occurred during file conversion.';
                });
        });
    </script>
@endsection
