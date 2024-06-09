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
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Enter the text
                            you want to
                            convert into a barcode.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Generate Barcode"
                            button.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> The barcode will be generated
                            and automatically downloaded.</li>
                    </ul>
                </div>
            </div>

            <!-- Barcode form column -->
            <div class="col-md-6">
                <div class="panel-heading mt-5">
                    <h2 class="text-center">Barcode Generator</h2>
                </div>
                <form id="barcode-form" action="/generate-barcode" method="GET" class="mt-4">
                    <label for="promptInput" class="form-label" style="font-weight: bold">Enter barcode text:</label>
                    <div class="input-group mb-3">

                        <input type="text" name="barcode_text" id="barcode-text" class="form-control"
                            placeholder="e.g., ABC123" aria-label="Enter Barcode Text" aria-describedby="generate-button">
                        <button type="submit" id="generate-button" class="btn btn-primary"
                            style="border-radius: 0 5px 5px 0;">Generate Barcode <i
                                class="fa-solid fa-barcode"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('barcode-form').addEventListener('submit', function(event) {
            event.preventDefault();

            // Get the barcode text
            const barcodeText = document.getElementById('barcode-text').value.trim();

            if (barcodeText === '') {
                alert('Please enter barcode text');
                return;
            }

            fetch(this.action + '?' + new URLSearchParams(new FormData(this)))
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('Failed to generate barcode');
                })
                .then(blob => {
                    // Create a temporary link element to trigger the download
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Barcode.png';
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
