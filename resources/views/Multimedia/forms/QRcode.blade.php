@extends('layouts.app')
<title>QR Code Maker</title>
@section('content')
    <form action="/generate-qr-code" method="GET" class="mt-7">
        <div class="input-group mb-3">
            <input type="text" name="url" class="form-control" placeholder="Enter URL" aria-label="Enter URL"
                aria-describedby="generate-button">
            <button class="btn btn-primary" type="submit" id="generate-button">Generate QR Code</button>
        </div>
    </form>


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
