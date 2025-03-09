@extends('layouts.app')
<title>PDF Merger</title>
@section('content')
<style>
    body {
        background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    .file-input {
        margin: 20px 0;
    }
    #pdf-list {
        margin: 10px 0;
    }
    .pdf-item {
        display: flex;
        align-items: center;
        margin: 5px 0;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 4px;
        cursor: move; /* Menunjukkan item bisa diseret */
    }
    .pdf-item.dragging {
        opacity: 0.5; /* Efek visual saat diseret */
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
</style>

<div class="container mt-6 mb-5">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Upload PDF files</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Arrange merge order (drag to reorder)</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Click Merge PDFs</li>
                    <li class="guideline-step"><span style="font-weight: bolder">4.</span> Download merged result</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="position-relative">
                <h3>Upload PDF Files</h3>
                <input type="file" class="form-control file-input" id="pdf-upload" multiple accept="application/pdf">
                <div id="pdf-list"></div>
                <button id="merge-btn" class="btn btn-primary mt-2">
                    <i class="fas fa-file-pdf"></i> Merge PDF
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
        const pdfList = document.getElementById('pdf-list');
        const uploadInput = document.getElementById('pdf-upload');
        const mergeBtn = document.getElementById('merge-btn');
        const resultDiv = document.getElementById('result');
        const progressBar = document.querySelector('.progress-bar');
        const progress = document.querySelector('.progress');

        let filesArray = []; // Array untuk menyimpan file dalam urutan yang diubah

        uploadInput.addEventListener('change', updateFileList);

        function updateFileList() {
            filesArray = Array.from(uploadInput.files); // Simpan file awal ke array
            renderFileList();
        }

        function renderFileList() {
            pdfList.innerHTML = '';
            filesArray.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'pdf-item';
                div.draggable = true; // Aktifkan draggable
                div.dataset.index = index; // Simpan indeks untuk referensi
                div.innerHTML = `
                    <span style="margin-right: 10px;">${index + 1}.</span>
                    <span style="flex-grow: 1;">${file.name}</span>
                    <button class="btn btn-sm btn-danger remove-btn" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                pdfList.appendChild(div);
            });
        }

        // Drag and Drop Logic
        pdfList.addEventListener('dragstart', (e) => {
            const item = e.target.closest('.pdf-item');
            if (item) {
                item.classList.add('dragging');
                e.dataTransfer.setData('text/plain', item.dataset.index);
            }
        });

        pdfList.addEventListener('dragend', (e) => {
            const item = e.target.closest('.pdf-item');
            if (item) {
                item.classList.remove('dragging');
            }
        });

        pdfList.addEventListener('dragover', (e) => {
            e.preventDefault(); // Izinkan drop
        });

        pdfList.addEventListener('drop', (e) => {
            e.preventDefault();
            const draggedIndex = e.dataTransfer.getData('text/plain');
            const dropTarget = e.target.closest('.pdf-item');
            if (dropTarget) {
                const dropIndex = dropTarget.dataset.index;

                // Tukar posisi file di filesArray
                const [draggedFile] = filesArray.splice(draggedIndex, 1);
                filesArray.splice(dropIndex, 0, draggedFile);

                renderFileList(); // Perbarui tampilan
            }
        });

        pdfList.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-btn');
            if (btn) {
                const index = btn.dataset.index;
                removeFile(index);
            }
        });

        function removeFile(index) {
            filesArray.splice(index, 1); // Hapus dari array
            renderFileList(); // Perbarui tampilan
        }

        mergeBtn.addEventListener('click', async () => {
            if (filesArray.length < 2) {
                alert('Please select at least 2 PDF files');
                return;
            }

            const formData = new FormData();
            filesArray.forEach(file => {
                formData.append('pdfs[]', file); // Kirim file sesuai urutan baru
            });

            try {
                mergeBtn.disabled = true;
                mergeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Merging...';
                progressBar.style.display = 'block';
                progress.style.width = '0%';

                const response = await fetch('/merge-pdfs', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Merge failed');
                }

                progress.style.width = '100%';
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                resultDiv.innerHTML = `
                    <a href="${url}" class="btn btn-success" download="merged.pdf">
                        <i class="fas fa-download"></i> Download Merged PDF
                    </a>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            } finally {
                mergeBtn.disabled = false;
                mergeBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Merge PDFs';
                setTimeout(() => {
                    progressBar.style.display = 'none';
                    progress.style.width = '0%';
                }, 1000);
            }
        });
    });
</script>
@endsection