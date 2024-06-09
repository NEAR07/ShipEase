@extends('layouts.app')
<title>QR Code Maker</title>
@section('content')
    <style>
        body {
            background-image: url("{{ asset('assets/img/bg2.jpeg') }}");
            background-size: cover;
        }
    </style>
    <div class="container mt-4">
        <div class="row justify-content-center py-6">
            <!-- Guideline box column -->
            <div class="col-md-3">
                <div class="guideline-box">
                    <h4 class="guideline-title text-center">Steps to follow</h4>
                    <ul class="list-unstyled">
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Enter the url
                            you want to
                            convert into a QR code.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Generate QR Code"
                            button.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> The QR Code will be generated
                            and automatically downloaded.</li>
                    </ul>
                </div>
            </div>

            <!-- Barcode form column -->
            <div class="col-md-6">
                <div class="panel-heading mt-5">
                    <h2 class="text-center">QR Code Generator</h2>
                </div>
                <form action="/generate-qr-code" method="GET" class="mt-4">
                    <label for="promptInput" class="form-label" style="font-weight: bold">Enter destination URL:</label>
                    <div class="input-group mb-3">
                        <input type="text" name="url" class="form-control" placeholder="e.g., www.xyz.com"
                            aria-label="Enter URL" aria-describedby="generate-button">
                        <button class="btn btn-primary" type="submit" id="generate-button"
                            style="border-radius: 0 5px 5px 0;">Generate QR Code <i class="fa-solid fa-qrcode"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();

            // Submit the form asynchronously
            fetch('/generate-qr-code?url=' + encodeURIComponent(document.querySelector('input[name="url"]').value))
                .then(response => {
                    // Trigger download if the response is successful
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('Failed to generate QR code');
                })
                .then(blob => {
                    // Create a temporary link element to trigger the download
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'QRcode.svg';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
@endsection
