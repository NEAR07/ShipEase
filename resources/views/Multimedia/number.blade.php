@extends('layouts.app')
<title>Penomoran PDF</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<style>
    body {
        /* background-image: url("{{ asset('assets/img/bg3.jpeg') }}"); */
        background-image: url("{{ asset('assets/img/2nd bg.png') }}");
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    .container {
        margin-top: 60px;
        margin-bottom: 50px;
        max-width: 1200px;
    }
    .guideline-box {
        background-color: rgba(11, 10, 10, 0.5);
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
        color: #fff;
    }
    .form-container {
        background-color: rgba(0, 0, 0, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        color: #fff;
    }
    .pdf-viewer {
        width: 100%;
        height: 400px;
        border: 1px solid #ccc;
        background-color: #fff;
        overflow: auto;
    }
    .info-label {
        font-size: 16px;
        font-weight: bold;
        margin: 10px 0;
        text-align: center;
    }
    .form-section {
        margin: 10px 0;
    }
    .form-section label {
        font-size: 14px;
        font-weight: bold;
    }
    .form-section input, .form-section select {
        font-size: 14px;
        width: 100%;
        padding: 5px;
        color: #000;
    }
    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .button-group button {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: bold;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .upload-btn {
        background-color: #4CAF50;
    }
    .back-btn {
        background-color: #FFC107;
    }
    .next-btn {
        background-color: #2196F3;
    }
    .finish-btn {
        background-color: #FF5722;
    }
    .quit-btn {
        background-color: #9E9E9E;
    }
    .button-group button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .progress-bar {
        width: 100%;
        height: 20px;
        background-color: #f1f1f1;
        border-radius: 4px;
        display: none;
        margin-top: 10px;
    }
    .progress {
        height: 100%;
        background-color: #007bff;
        width: 0%;
        transition: width 0.3s;
    }
    .thumbnail-img {
        width: 100%;
        cursor: pointer;
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
        <img src="{{ asset('assets/img/before-after/numberingPDF.jpg') }}" alt="Iklan" class="ad-img">
        <button class="ad-next-btn" id="adNextBtn">Next</button>
    </div>
</div>

<div class="container" id="mainApp">
    <div class="row">
        <!-- Kotak Panduan -->
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Start by Uploading PDF Files</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Enter Block and Sheet Numbers</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Navigate Pages</li>
                    <li class="guideline-step"><span style="font-weight: bolder">4.</span> Finalize the Process</li>
                    <li class="guideline-step"><span style="font-weight: bolder">5.</span> Reset if Necessary</li>
                </ul>
                <!-- <h4 class="guideline-title text-center">Proses</h4>
                <img src="{{ asset('assets/img/before-after/numberingPDF.jpg') }}" alt="Gambar Proses" class="thumbnail-img" id="thumbnail"> -->
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9">
            <div class="form-container">
                <h3 class="text-center">Numbering Block & Sheet e-Ticket Nesting Plate</h3>

                <!-- Pratinjau PDF -->
                <div id="pdf-viewer" class="pdf-viewer"></div>

                <!-- Label Informasi -->
                <div id="info-label" class="info-label">Upload a PDF file to start.</div>

                <!-- Form Input -->
            <div class="form-section">
                <label for="paper-size">Select Paper Size:</label>
                <select id="paper-size" class="form-control">
                    <option value="A3" selected>A3</option>
                    <!-- Tambahkan opsi lain jika diperlukan -->
                    <!-- <option value="A4">A4</option> -->
                    <!-- <option value="A5">A5</option> -->
                </select>
            </div>

            <div class="form-section">
                <label for="block-input">Block Number:</label>
                <input type="text" id="block-input" class="form-control">
            </div>

            <div class="form-section">
                <label for="sheet-input">Sheet Number:</label>
                <input type="text" id="sheet-input" class="form-control">
            </div>

            <!-- Tombol Aksi -->
            <div class="button-group">
                <button id="upload-btn" class="upload-btn">Upload PDF</button>
                <button id="back-btn" class="back-btn" disabled>Previous Page</button>
                <button id="next-btn" class="next-btn" disabled>Next Page</button>
                <button id="finish-btn" class="finish-btn" disabled>Finish</button>
                <button id="quit-btn" class="quit-btn">Reset</button>
            </div>

                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
                <div id="message" class="info-label" style="color: #d9534f;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
// JavaScript tetap sama, hanya diterjemahkan ke dalam bahasa Indonesia untuk komentar
document.addEventListener('DOMContentLoaded', () => {
    const pdfViewer = document.getElementById('pdf-viewer');
    const infoLabel = document.getElementById('info-label');
    const paperSize = document.getElementById('paper-size');
    const blockInput = document.getElementById('block-input');
    const sheetInput = document.getElementById('sheet-input');
    const uploadBtn = document.getElementById('upload-btn');
    const backBtn = document.getElementById('back-btn');
    const nextBtn = document.getElementById('next-btn');
    const finishBtn = document.getElementById('finish-btn');
    const quitBtn = document.getElementById('quit-btn');
    const messageDiv = document.getElementById('message');
    const progressBar = document.querySelector('.progress-bar');
    const progress = document.querySelector('.progress');
    const adContainer = document.getElementById('adContainer');
    const adNextBtn = document.getElementById('adNextBtn');
    const mainApp = document.getElementById('mainApp');

    let pdfFile = null;
    let pdfDoc = null;
    let currentPage = 0;
    let totalPages = 0;
    let pageData = {};

    // Inisialisasi pdf.js
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = '//cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

    // Logika untuk iklan
    adNextBtn.addEventListener('click', () => {
        adContainer.style.display = 'none';
        mainApp.style.display = 'block';
    });

    function renderPage(pageNum) {
        pdfDoc.getPage(pageNum + 1).then(page => {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: 1.5 });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            page.render({
                canvasContext: context,
                viewport: viewport
            }).promise.then(() => {
                pdfViewer.innerHTML = '';
                pdfViewer.appendChild(canvas);
            });

            infoLabel.textContent = `Mengedit halaman ${pageNum + 1} dari ${totalPages}`;
            backBtn.disabled = pageNum === 0;
            nextBtn.disabled = pageNum === totalPages - 1;

            if (pageData[pageNum]) {
                blockInput.value = pageData[pageNum].block || '';
                sheetInput.value = pageData[pageNum].sheet || '';
            } else {
                blockInput.value = '';
                sheetInput.value = '';
            }
        });
    }

    uploadBtn.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf';
        input.onchange = async (e) => {
            pdfFile = e.target.files[0];
            if (!pdfFile) return;

            const arrayBuffer = await pdfFile.arrayBuffer();
            pdfDoc = await pdfjsLib.getDocument(arrayBuffer).promise;
            totalPages = pdfDoc.numPages;
            currentPage = 0;
            pageData = {};

            renderPage(currentPage);
            nextBtn.disabled = totalPages <= 1;
            finishBtn.disabled = false;
        };
        input.click();
    });

    backBtn.addEventListener('click', () => {
        saveCurrentPageData();
        if (currentPage > 0) {
            currentPage--;
            renderPage(currentPage);
        }
    });

    nextBtn.addEventListener('click', () => {
        saveCurrentPageData();
        if (currentPage < totalPages - 1) {
            currentPage++;
            renderPage(currentPage);
        }
    });

    function saveCurrentPageData() {
        pageData[currentPage] = {
            block: blockInput.value,
            sheet: sheetInput.value
        };
    }

    finishBtn.addEventListener('click', async () => {
        saveCurrentPageData();
        if (!pdfFile) {
            messageDiv.textContent = 'Harap unggah file PDF terlebih dahulu.';
            return;
        }

        const formData = new FormData();
        formData.append('pdf', pdfFile);
        formData.append('page_data', JSON.stringify(pageData));

        try {
            finishBtn.disabled = true;
            finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            progressBar.style.display = 'block';
            progress.style.width = '0%';

            const response = await fetch('/number-pdf', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Penomoran gagal');
            }

            progress.style.width = '100%';
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = pdfFile.name.replace('.pdf', '_numbered.pdf');
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            messageDiv.textContent = 'PDF berhasil diberi nomor dan diunduh.';
        } catch (error) {
            messageDiv.textContent = `Kesalahan: ${error.message}`;
        } finally {
            finishBtn.disabled = false;
            finishBtn.innerHTML = 'Selesai';
            setTimeout(() => {
                progressBar.style.display = 'none';
                progress.style.width = '0%';
            }, 1000);
        }
    });

    quitBtn.addEventListener('click', () => {
        pdfFile = null;
        pdfDoc = null;
        currentPage = 0;
        totalPages = 0;
        pageData = {};
        pdfViewer.innerHTML = '';
        infoLabel.textContent = 'Unggah file PDF untuk memulai.';
        blockInput.value = '';
        sheetInput.value = '';
        backBtn.disabled = true;
        nextBtn.disabled = true;
        finishBtn.disabled = true;
        messageDiv.textContent = '';
    });
});
</script>
@endsection