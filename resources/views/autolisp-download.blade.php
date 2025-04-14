@extends('layouts.app')
<title>Download AutoLISP Tools for ZWCAD</title>

@section('content')
<style>
    body {
    background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
    background-size: cover;
    background-position: center;
    font-family: 'Poppins', Arial, sans-serif; 
    color: #f9f9f9;
    min-height: 100vh;
    }

    .container {
        margin-top: 80px; 
        margin-bottom: 60px;
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
    background-color: rgb(0, 0, 0);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.1); 
    }

    .btn-primary {
        background: linear-gradient(90deg, #355887, #4a7bb8); 
        border: none;
        padding: 12px 25px;
        font-size: 16px;
        border-radius: 25px; 
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #b30000, #e63939);
        transform: scale(1.05); 
    }

    .file-list label {
        display: block;
        padding: 15px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .file-list label:hover {
        background: rgba(255, 255, 255, 0.1); 
    }

    .file-list input[type="radio"] {
        margin-right: 10px;
        accent-color: #355887; 
    }

    .alert {
        margin-top: 15px;
        border-radius: 8px;
        padding: 12px;
    }
    .file-list {
        margin-top: 20px;
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
        <img src="{{ asset('assets/img/before-after/zwcad.jpg') }}" alt="Iklan" class="ad-img">
        <button class="ad-next-btn" id="adNextBtn">Next</button>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="guideline-box">
                <h4 class="guideline-title text-center">Steps to Follow</h4>
                <ul class="list-unstyled">
                    <li class="guideline-step"><span style="font-weight: bolder">1.</span> Select an AutoLISP tool</li>
                    <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the download button</li>
                    <li class="guideline-step"><span style="font-weight: bolder">3.</span> Load in ZWCAD using APPLOAD</li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            <div class="form-container">
                <h3>Download AutoLISP Tools for ZWCAD</h3>
                
                <p>Select and download AutoLISP tools from the list below:</p>
                
                <form action="{{ route('autolisp.download') }}" method="POST" id="download-form">
                    @csrf
                    <div class="file-list">
                        @foreach($files as $filename => $description)
                            <div class="mb-2">
                                <label>
                                    <input type="radio" name="filename" value="{{ $filename }}" required>
                                    <span>
                                        @if($filename === 'CMB1.lsp')
                                            ({{ $filename }} + database.csv)
                                            <br>
                                            <span style="text-align: justify; display: block;">{{ $description }}</span>
                                        @else
                                            ({{ $filename }})
                                            <br>
                                            <span style="text-align: justify; display: block;">{{ $description }}</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary" id="download-btn">
                            <i class="fas fa-download"></i> Download Now
                        </button>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger mt-3 text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div id="result" class="mt-3 text-center"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const downloadBtn = document.getElementById('download-btn');
        const resultDiv = document.getElementById('result');
        const form = document.getElementById('download-form');

                // Logika untuk iklan
        adNextBtn.addEventListener('click', () => {
            adContainer.style.display = 'none';
            mainApp.style.display = 'block';
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (downloadBtn.disabled) return;

            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            resultDiv.innerHTML = '';

            try {
                await new Promise(resolve => setTimeout(resolve, 500)); // Simulasi proses
                form.submit();
                
                await new Promise(resolve => setTimeout(resolve, 500));
                resultDiv.innerHTML = '<div class="alert alert-success fade-in">Download completed successfully!</div>';
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger fade-in">Error: ${error.message}</div>`;
            } finally {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = '<i class="fas fa-download"></i> Download Now';
            }
        });
    });

    // CSS tambahan untuk animasi fade-in
    const style = document.createElement('style');
    style.innerHTML = `
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    document.head.appendChild(style);

</script>
@endsection