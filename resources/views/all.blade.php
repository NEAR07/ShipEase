@extends('layouts.app')
<title>Tools</title>
<style>
    body {
        background-image: url("{{ asset('assets/img/bg5.jpeg') }}");
        background-size: cover;
    }

    .card-link {
        display: block;
        text-decoration: none;
    }

    .card {
        transition: transform 0.2s;
        background-color: #fd7014 !important;
    }

    .card:hover {
        transform: translateY(-3px);
        background-color: #f0f0f0 !important;
    }

    .card-link .card-body h4,
    .card-link .card-body span,
    .card-link .card-body p,
    .card-link .card-body i {
        color: #0b0a0a;
    }

    span {
        margin-top: -10px;
        margin-bottom: 10px;
    }

    /* Styling for search bar */
    .search-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }

    .search-input {
    padding: 10px;
    width: 300px;
    border: 2px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    color: white; /* Menambahkan warna teks putih */
    background-color: #333; /* Opsional: Mengubah latar belakang input agar kontras dengan teks putih */
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.7); /* Mengubah warna placeholder menjadi abu-abu terang */
}

.search-input:focus {
    outline: none;
    border-color: #fd7014;
    box-shadow: 0 0 5px rgba(253, 112, 20, 0.5);
    color: white; /* Memastikan warna teks tetap putih saat input difokuskan */
}
</style>
@section('content')
    <div class="container mt-6">
        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Search tools...">
        </div>

        <!-- Tools Cards -->
        <div class="row py-6" id="toolsContainer">
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/merge_pdf') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Merge and <br>Manage  PDF</h4>
                                    <span class="badge badge-info">File tool</span>
                                    <p>Combine and organize multiple PDF files effortlessly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/number-pdf') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Numbering Eticket NestProf</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Automate numbering and nesting profiles for e-tickets</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/partlist-converter') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Convert Data <br>Cadmatic to Excel</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Convert Cadmatic nesting lists to Excel for data management</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/partnameConverter') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>PartName Merger<br></h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Merge partname from DXF files into a single, organized output</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/download') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Resume Matlist<br></h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Resume Material List All Block Excel Based Application</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/compare-download') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Revision Comparator<br></h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Compare Matlist, Partlist, and APL revisions in Excel</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/autolisp-download') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>ZWCAD<br>ScriptMaster</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>A collection of AutoLISP scripts to automate CAD projects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/word-to-pdf') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Word to PDF Converter</h4>
                                    <span class="badge badge-info">File Tool</span>
                                    <p>Convert Word file to PDF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/qr-code') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>QR Code Maker</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Generate premium QR Codes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/barcode') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>Barcode Maker</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Generate easy-to-use barcodes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{ url('/url-shortener') }}" class="card-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fa-solid fa-3x fa-gear"></i>
                                </div>
                                <div class="col">
                                    <h4>URL Shortener</h4>
                                    <span class="badge badge-info">Utility Tool</span>
                                    <p>Shorten your links</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Save the initial order of the cards
        const toolsContainer = document.getElementById('toolsContainer');
        const originalOrder = Array.from(toolsContainer.getElementsByClassName('col-md-6'));

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            if (searchTerm.trim() === '') {
                // If search is cleared, restore the original order
                originalOrder.forEach(tool => {
                    tool.style.display = 'block';
                    toolsContainer.appendChild(tool); // Re-append in original order
                });
                return;
            }

            // Filter and sort tools based on relevance
            const tools = Array.from(toolsContainer.getElementsByClassName('col-md-6'));
            tools.sort((a, b) => {
                const titleA = a.querySelector('.card-body h4').innerText.toLowerCase();
                const descriptionA = a.querySelector('.card-body p').innerText.toLowerCase();
                const titleB = b.querySelector('.card-body h4').innerText.toLowerCase();
                const descriptionB = b.querySelector('.card-body p').innerText.toLowerCase();

                const matchA = (titleA.includes(searchTerm) ? 1 : 0) + (descriptionA.includes(searchTerm) ? 1 : 0);
                const matchB = (titleB.includes(searchTerm) ? 1 : 0) + (descriptionB.includes(searchTerm) ? 1 : 0);

                return matchB - matchA; // Sort descending by relevance
            });

            // Re-append sorted tools to the container
            tools.forEach(tool => {
                const title = tool.querySelector('.card-body h4').innerText.toLowerCase();
                const description = tool.querySelector('.card-body p').innerText.toLowerCase();

                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    tool.style.display = 'block';
                    toolsContainer.appendChild(tool); // Move matching tools to the top
                } else {
                    tool.style.display = 'none'; // Hide non-matching tools
                }
            });
        });
    </script>
@endsection