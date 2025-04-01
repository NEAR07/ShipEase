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
    .file-input {
        margin: 15px 0;
    }
    .file-item {
        display: flex;
        align-items: center;
        margin: 5px 0;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    #file-list {
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
    .btn-danger {
        background-color: #dc3545;
        border: none;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .btn-success {
        margin-right: 10px;
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
    .form-container {
        background-color: rgba(0, 0, 0, 0.95);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .alert {
        margin-top: 10px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Select a folder containing <b>.dxf</b> files</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click "Process DXF Files"</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Download processed files as a folder</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-container">
                <h3>Convert Partname in DXF</h3>
                <form id="convert-form" action="{{ route('partnameConverter.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="file-input">
                        <label for="dxfFiles" class="form-label" style="font-weight: bold">Select DXF Folder:</label>
                        <input type="file" name="dxf_files[]" id="dxfFiles" class="form-control" accept=".dxf" webkitdirectory mozdirectory msdirectory odirectory directory multiple required>
                        @error('dxf_files.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="file-list"></div>

                    <button type="submit" id="convert-btn" class="btn btn-primary mt-2" disabled>
                        <i class="fas fa-cogs"></i> Process DXF Files
                    </button>
                </form>

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
        const form = document.getElementById('convert-form');
        const convertBtn = document.getElementById('convert-btn');
        const resultDiv = document.getElementById('result');
        const progressBar = document.querySelector('.progress-bar');
        const progress = document.querySelector('.progress');
        const fileInput = document.getElementById('dxfFiles');
        const fileList = document.getElementById('file-list');
        let files = [];

        fileInput.addEventListener('change', () => {
            files = Array.from(fileInput.files);
            updateFileList();
        });

        function updateFileList() {
            fileList.innerHTML = '';
            convertBtn.disabled = files.length === 0;

            files.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'file-item';
                div.innerHTML = `
                    <span style="margin-right: 10px;">${index + 1}.</span>
                    <span style="flex-grow: 1;">${file.webkitRelativePath || file.name}</span>
                    <button class="btn btn-sm btn-danger remove-btn" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileList.appendChild(div);
            });
        }

        fileList.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-btn');
            if (btn) {
                const index = parseInt(btn.dataset.index);
                files.splice(index, 1);
                updateFileList();
                const dt = new DataTransfer();
                files.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (files.length === 0) {
                resultDiv.innerHTML = `<div class="alert alert-warning">Please select a folder containing at least one .dxf file</div>`;
                return;
            }

            const formData = new FormData();
            files.forEach(file => formData.append('dxf_files[]', file));

            try {
                convertBtn.disabled = true;
                convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                progressBar.style.display = 'block';
                progress.style.width = '0%';
                resultDiv.innerHTML = '';

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Processing failed');
                }

                progress.style.width = '100%';
                const data = await response.json();
                resultDiv.innerHTML = data.log ? `<div class="alert alert-info">${data.log.replace(/\n/g, '<br>')}</div>` : '';

                if (data.output_files && data.output_files.length > 0) {
                    // Tombol untuk unduh folder sebagai ZIP
                    const zipUrl = `${form.action}/zip`;
                    resultDiv.innerHTML += `
                        <a href="#" id="download-zip" class="btn btn-success mt-2" data-files='${JSON.stringify(data.output_files)}'>
                            <i class="fas fa-file-archive"></i> Download Processed Folder as ZIP
                        </a>
                    `;

                    // Logika untuk unduh ZIP
                    document.getElementById('download-zip').addEventListener('click', async (e) => {
                        e.preventDefault();
                        const outputFiles = JSON.parse(e.target.dataset.files);
                        const fileUrls = outputFiles.map(file => file.url);

                        const zipResponse = await fetch(zipUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/zip'
                            },
                            body: JSON.stringify({ files: fileUrls })
                        });

                        if (!zipResponse.ok) {
                            throw new Error('Failed to create ZIP');
                        }

                        const blob = await zipResponse.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'processed_dxf_files.zip';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    });
                }
            } catch (error) {
                console.error('Fetch error:', error.message);
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            } finally {
                convertBtn.disabled = false;
                convertBtn.innerHTML = '<i class="fas fa-cogs"></i> Process DXF Files';
                setTimeout(() => {
                    progressBar.style.display = 'none';
                    progress.style.width = '0%';
                }, 1000);
            }
        });
    });
</script>
@endsection