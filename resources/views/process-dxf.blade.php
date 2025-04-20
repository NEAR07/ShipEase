@extends('layouts.app')
<title>DXF Processor</title>

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
</style>

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
                <h3>CAD Block Grouping Based on End Cut Type</h3>
                <p>Download our standalone application to process your DXF files locally.</p>

                <a href="{{ route('download.app') }}" id="download-btn" class="btn btn-primary mt-2">
                    <i class="fas fa-download"></i> Download App (719 MB)
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

        function appendLog(message) {
            logContainer.style.display = 'block';
            const logEntry = document.createElement('div');
            logEntry.textContent = message;
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        downloadBtn.addEventListener('click', async (e) => {
            // Tidak perlu preventDefault karena ini hanya link biasa
            appendLog('Starting download of PartName Merger.zip...');
            resultDiv.innerHTML = '<div class="alert alert-info">Download in progress...</div>';

            // Catatan: Karena ini unduhan langsung via link, kita tidak bisa menangani respons di sini.
            // Jika ingin log lebih detail, gunakan fetch di controller.
            setTimeout(() => {
                appendLog('Download initiated. Please check your downloads folder.');
                resultDiv.innerHTML = '<div class="alert alert-success">Download initiated successfully!</div>';
            }, 1000);
        });
    });
</script>
@endsection