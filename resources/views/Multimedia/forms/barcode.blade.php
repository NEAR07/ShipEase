@extends('layouts.app')
<title>QR Code Maker</title>
@section('content')
    <form id="barcode-form" action="/generate-barcode" method="GET" class="mt-7">
        <div class="input-group mb-3">
            <input type="text" name="barcode_text" id="barcode-text" class="form-control" placeholder="Enter Barcode Text"
                aria-label="Enter Barcode Text" aria-describedby="generate-button">
            <button type="submit" id="generate-button" class="btn btn-primary">Generate Barcode</button>
        </div>
    </form>



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
