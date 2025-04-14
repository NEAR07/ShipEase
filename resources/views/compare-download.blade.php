@extends('layouts.app')
<title>Download Compare Data Excel</title>

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
    .btn-primary {
        background-color: #355887;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
    }
    .btn-primary:hover {
        background-color: #b30000;
    }
    .form-container {
        background-color: rgba(0, 0, 0, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .alert {
        margin-top: 10px;
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
        <img src="{{ asset('assets/img/before-after/compare revisi partlist, matlist, apl.jpg') }}" alt="Iklan" class="ad-img">
        <button class="ad-next-btn" id="adNextBtn">Next</button>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Click the download button</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Save the <b>.xls</b> file</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Open with Excel to use</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-container">
                <h3>Compare changes in Matlist, Partlist, and APL revisions directly in Excel</h3>
                
                <p>Click the button below to download the Compare Data Excel application :</p>
                
                <a href="{{ route('compare.download') }}" 
                   class="btn btn-primary" 
                   id="download-btn">
                    <i class="fas fa-download"></i> Download file
                </a>

                <div id="result" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const downloadBtn = document.getElementById('download-btn');
        const resultDiv = document.getElementById('result');

            // Logika untuk iklan
        adNextBtn.addEventListener('click', () => {
            adContainer.style.display = 'none';
            mainApp.style.display = 'block';
        });

        downloadBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            if (downloadBtn.disabled) return;

            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            resultDiv.innerHTML = '';

            try {
                // Simulasi proses download
                await new Promise(resolve => setTimeout(resolve, 500));
                
                window.location.href = downloadBtn.href;
                
                await new Promise(resolve => setTimeout(resolve, 500));
                resultDiv.innerHTML = '<div class="alert alert-success">File downloaded successfully!</div>';
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            } finally {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = '<i class="fas fa-download"></i> Download CEK MATLIST.xls';
            }
        });
    });
</script>
@endsection