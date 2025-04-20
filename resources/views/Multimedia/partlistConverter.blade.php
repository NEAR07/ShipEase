@extends('layouts.app')
<title>Partlist Converter</title>
@section('content')
<style>
    body {
        /* background-image: url("{{ asset('assets/img/bg3.jpeg') }}"); */
        background-image: url("{{ asset('assets/img/2nd bg.png') }}");
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    .file-input {
        margin: 20px 0;
    }
    .input-label {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .progress-bar {
        width: 100%;
        height: 20px;
        background-color: #f1f1f1;
        border-radius: 4px;
        display: none;
        overflow: hidden;
    }
    .progress {
        height: 100%;
        background-color: #007bff;
        width: 0%;
        transition: width 0.3s;
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
        <img src="{{ asset('assets/img/before-after/cadmatic to excel.jpg') }}" alt="Iklan" class="ad-img">
        <button class="ad-next-btn" id="adNextBtn">Next</button>
    </div>
</div>

<div class="container mt-6 mb-5">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Upload Profil Nesting List (.list or .lst)</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Upload Profil (.list or .lst)</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Upload Database (.csv)</li>
                    <li class="guideline-step"><span style="font-weight: bolder">4.</span> Click Convert to Excel</li>
                    <li class="guideline-step"><span style="font-weight: bolder">5.</span> Download the Excel file</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="position-relative">
                <h3>Upload Files</h3>
                
                <div class="file-input">
                    <div class="input-label">Profil Nesting List (.list or .lst)</div>
                    <input type="file" class="form-control" id="list-upload" accept=".list,.lst" required>
                </div>
                
                <div class="file-input">
                    <div class="input-label">Report for Profil Nesting (.list or .lst)</div>
                    <input type="file" class="form-control" id="lst-upload" accept=".list,.lst" required>
                </div>
                
                <div class="file-input">
                    <div class="input-label">Database (.csv)</div>
                    <input type="file" class="form-control" id="csv-upload" accept=".csv" required>
                </div>

                <button id="convert-btn" class="btn btn-primary mt-2">
                    <i class="fas fa-file-excel"></i> Convert to Excel
                </button>
                <div class="progress-bar mt-2">
                    <div class="progress"></div>
                </div>
                <div id="result" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const listUpload = document.getElementById('list-upload');
        const lstUpload = document.getElementById('lst-upload');
        const csvUpload = document.getElementById('csv-upload');
        const convertBtn = document.getElementById('convert-btn');
        const resultDiv = document.getElementById('result');
        const progressBar = document.querySelector('.progress-bar');
        const progress = document.querySelector('.progress');

            // Logika untuk iklan
        adNextBtn.addEventListener('click', () => {
            adContainer.style.display = 'none';
            mainApp.style.display = 'block';
        });
        // Tambahkan di bagian <script> di partlistConverter.blade.php
        convertBtn.addEventListener('click', async () => {
            if (!listUpload.files.length || !lstUpload.files.length || !csvUpload.files.length) {
                alert('Please upload all required files (Profil Nesting List, Profil, Database)');
                return;
            }

            const listExt = listUpload.files[0].name.split('.').pop().toLowerCase();
            const lstExt = lstUpload.files[0].name.split('.').pop().toLowerCase();
            const csvExt = csvUpload.files[0].name.split('.').pop().toLowerCase();

            if (!['list', 'lst'].includes(listExt) || !['list', 'lst'].includes(lstExt) || csvExt !== 'csv') {
                alert('Invalid file types. Please upload .list or .lst for Profil Nesting List and Profil, and .csv for Database.');
                return;
            }

            const formData = new FormData();
            formData.append('list_file', listUpload.files[0]);
            formData.append('lst_file', lstUpload.files[0]);
            formData.append('csv_file', csvUpload.files[0]);

            try {
                console.log('Sending request to /partlist-converter'); // Debugging
                convertBtn.disabled = true;
                convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Converting...';
                progressBar.style.display = 'block';
                progress.style.width = '0%';

                const response = await fetch("{{ route('partlist.convert') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                console.log('Response received:', response.status); // Debugging

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Conversion failed');
                }

                progress.style.width = '100%';
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                resultDiv.innerHTML = `
                    <a href="${url}" class="btn btn-success" download="converted.xlsx">
                        <i class="fas fa-download"></i> Download Excel File
                    </a>
                `;
            } catch (error) {
                console.error('Error during request:', error); // Debugging
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            } finally {
                convertBtn.disabled = false;
                convertBtn.innerHTML = '<i class="fas fa-file-excel"></i> Convert to Excel';
                setTimeout(() => {
                    progressBar.style.display = 'none';
                    progress.style.width = '0%';
                }, 1000);
            }
        });
    });
</script>
@endsection