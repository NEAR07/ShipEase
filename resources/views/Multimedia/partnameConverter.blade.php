@extends('layouts.app')
<title>Partname Converter</title>

@section('content')
<style>
    body {
        background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    .container {
        margin-top: 60px;
        margin-bottom: 50px;
    }
    .guideline-box {
        background-color: rgb(0, 0, 0);
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .guideline-title {
        color: rgb(249, 249, 249);
        font-weight: bold;
    }
    .guideline-step {
        margin: 10px 0;
    }
    .form-container {
        background-color: rgba(0, 0, 0, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-primary {
        background-color: #355887;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
    }
    .btn-primary:hover {
        background-color: #b30000;
    }
    .alert {
        margin-top: 10px;
    }
    #log-container {
        height: 150px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow-y: auto;
        padding: 10px;
        font-size: 12px;
        line-height: 1.2;
        margin-top: 10px;
        display: none; /* Sembunyikan kecuali ada log */
    }
    /* Styling untuk pop-up */
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .popup-content {
        position: relative;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        max-width: 80%;
        max-height: 80%;
        overflow: auto;
    }
    .popup-img {
        max-width: 100%;
        max-height: 500px;
    }
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
    }
    .close-btn:hover {
        color: #ff0000;
    }
    /* Styling untuk iklan */
    .ad-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Latar belakang semi-transparan */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999; /* Pastikan iklan di atas elemen lain */
    }
    .ad-content {
        display: flex;
        flex-direction: column; /* Susun elemen secara vertikal */
        align-items: center; /* Pusatkan elemen secara horizontal */
        text-align: center;
    }
    .ad-img {
        max-width: 80%;
        max-height: 70vh; /* Batasi tinggi gambar iklan */
    }
    .ad-next-btn {
        margin-top: 20px; /* Kurangi jarak antara gambar dan tombol */
        margin-right: 50px;
        padding: 10px 30px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        background-color: rgb(18, 56, 94); /* Warna biru seperti tombol Next */
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .ad-next-btn:hover {
        background-color: rgb(61, 104, 147); /* Warna biru lebih gelap saat hover */
    }
</style>

<!-- Kontainer untuk iklan -->
<div class="ad-container" id="adContainer">
    <div class="ad-content">
        <img src="{{ asset('assets/img/before-after/partnamge merge.jpg') }}" alt="Iklan" class="ad-img">
        <button class="ad-next-btn" id="adNextBtn">Next</button>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Click "Download App"</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Extract the ZIP file</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Run the app to process your DXF files</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-container">
                <h3>PartName Merger ZWCAD for DXF Files</h3>
                <p>Download our standalone application to process your DXF files locally.</p>

                <a href="{{ route('partnameConverter.download') }}" id="download-btn" class="btn btn-primary mt-2">
                    <i class="fas fa-download"></i> Download App
                </a>

                <div id="log-container"></div>
                <div id="result" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const downloadBtn = document.getElementById('download-btn');
        const resultDiv = document.getElementById('result');
        const logContainer = document.getElementById('log-container');

            // Logika untuk iklan
        adNextBtn.addEventListener('click', () => {
            adContainer.style.display = 'none';
            mainApp.style.display = 'block';
        });

        function appendLog(message) {
            logContainer.style.display = 'block';
            const logEntry = document.createElement('div');
            logEntry.textContent = message;
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        downloadBtn.addEventListener('click', async (e) => {
            appendLog('Starting download of PartName Merger.zip...');
            resultDiv.innerHTML = '<div class="alert alert-info">Download in progress...</div>';

            setTimeout(() => {
                appendLog('Download initiated. Please check your downloads folder.');
                resultDiv.innerHTML = '<div class="alert alert-success">Download initiated successfully!</div>';
            }, 1000);
        });
    });
</script>
@endsection

