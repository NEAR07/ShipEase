@extends('layouts.app')
<title>Number PDF</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<style>
    body {
        background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 1000px;
        margin-top: 20px;
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
        background-color: #4CAF50; /* Hijau */
    }
    .back-btn {
        background-color: #FFC107; /* Kuning */
    }
    .next-btn {
        background-color: #2196F3; /* Biru */
    }
    .finish-btn {
        background-color: #FF5722; /* Merah */
    }
    .quit-btn {
        background-color: #9E9E9E; /* Abu-abu */
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
</style>

<div class="container">
    <h2 class="text-center">Numbering PDF Eticket Nesting Profile</h2>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
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

    let pdfFile = null;
    let pdfDoc = null;
    let currentPage = 0;
    let totalPages = 0;
    let pageData = {};

    // Inisialisasi pdf.js
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc = '//cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

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

            infoLabel.textContent = `Editing page ${pageNum + 1} of ${totalPages}`;
            backBtn.disabled = pageNum === 0;
            nextBtn.disabled = pageNum === totalPages - 1;

            // Muat data halaman jika ada
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
            messageDiv.textContent = 'Please upload a PDF file first.';
            return;
        }

        const formData = new FormData();
        formData.append('pdf', pdfFile);
        console.log('Page data before sending:', pageData);
        formData.append('page_data', JSON.stringify(pageData));

        try {
            finishBtn.disabled = true;
            finishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
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
                throw new Error(errorData.error || 'Numbering failed');
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
            messageDiv.textContent = 'PDF numbered successfully and downloaded.';
        } catch (error) {
            messageDiv.textContent = `Error: ${error.message}`;
        } finally {
            finishBtn.disabled = false;
            finishBtn.innerHTML = 'Finish';
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
        infoLabel.textContent = 'Upload a PDF file to start.';
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