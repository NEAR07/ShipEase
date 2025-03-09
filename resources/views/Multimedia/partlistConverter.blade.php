@extends('layouts.app')
<title>Partlist Converter</title>
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
        background-color:rgb(0, 0, 0);
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .guideline-title {
        color:rgb(249, 249, 249);
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
</style>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Upload <b>.list</b>, <b>.lst</b>, and <b>.csv</b> files</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Specify output <b>.xlsx</b> file name</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Click "Convert to Excel"</li>
                    <li class="guideline-step"><span style="font-weight: bolder">4.</span> Download the result</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-container">
                <h3>Convert Partlist to Excel</h3>
                <form id="convert-form" action="{{ route('partlist.convert') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="file-input">
                        <label for="listFile" class="form-label" style="font-weight: bold">Upload .list File:</label>
                        <input type="file" name="list" id="listFile" class="form-control" accept=".list" required>
                        @error('list')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="file-input">
                        <label for="lstFile" class="form-label" style="font-weight: bold">Upload .lst File:</label>
                        <input type="file" name="lst" id="lstFile" class="form-control" accept=".lst" required>
                        @error('lst')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="file-input">
                        <label for="csvFile" class="form-label" style="font-weight: bold">Upload .csv File:</label>
                        <input type="file" name="csv" id="csvFile" class="form-control" accept=".csv" required>
                        @error('csv')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="file-list"></div>

                    <div class="file-input">
                        <label for="outputFile" class="form-label" style="font-weight: bold">Output .xlsx File Name:</label>
                        <input type="text" name="output" id="outputFile" class="form-control" placeholder="e.g., output.xlsx" required>
                        @error('output')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" id="convert-btn" class="btn btn-primary mt-2" disabled>
                        <i class="fas fa-file-excel"></i> Convert to Excel
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
        const fileList = document.getElementById('file-list');
        const inputs = {
            list: document.getElementById('listFile'),
            lst: document.getElementById('lstFile'),
            csv: document.getElementById('csvFile')
        };
        let files = {};

        Object.keys(inputs).forEach(type => {
            inputs[type].addEventListener('change', () => {
                if (inputs[type].files.length > 0) {
                    files[type] = inputs[type].files[0];
                    updateFileList();
                }
            });
        });

        function updateFileList() {
            fileList.innerHTML = '';
            let allFilesSelected = true;

            ['list', 'lst', 'csv'].forEach((type, index) => {
                if (files[type]) {
                    const div = document.createElement('div');
                    div.className = 'file-item';
                    div.innerHTML = `
                        <span style="margin-right: 10px;">${index + 1}.</span>
                        <span style="flex-grow: 1;">${files[type].name}</span>
                        <button class="btn btn-sm btn-danger remove-btn" data-type="${type}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(div);
                } else {
                    allFilesSelected = false;
                }
            });

            convertBtn.disabled = !allFilesSelected;
        }

        fileList.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-btn');
            if (btn) {
                const type = btn.dataset.type;
                delete files[type];
                inputs[type].value = '';
                updateFileList();
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (Object.keys(files).length !== 3) {
                resultDiv.innerHTML = `<div class="alert alert-warning">Please upload all required files (.list, .lst, .csv)</div>`;
                return;
            }

            const outputFileName = document.getElementById('outputFile').value;
            if (!outputFileName.endsWith('.xlsx')) {
                resultDiv.innerHTML = `<div class="alert alert-warning">Output file name must end with .xlsx</div>`;
                return;
            }

            const formData = new FormData();
            formData.append('list', files['list']);
            formData.append('lst', files['lst']);
            formData.append('csv', files['csv']);
            formData.append('output', outputFileName);

            try {
                convertBtn.disabled = true;
                convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Converting...';
                progressBar.style.display = 'block';
                progress.style.width = '0%';

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/octet-stream'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Conversion failed');
                }

                progress.style.width = '100%';
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                resultDiv.innerHTML = `
                    <a href="${url}" class="btn btn-success" download="${outputFileName}">
                        <i class="fas fa-download"></i> Download Excel File
                    </a>
                `;
            } catch (error) {
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