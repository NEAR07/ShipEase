<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Craft Away!</title>

    <!--Inter UI font-->
    <link href="https://rsms.me/inter/inter-ui.css" rel="stylesheet">

    <!--vendors styles-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <!-- Bootstrap CSS / Color Scheme -->
    <link rel="stylesheet" href="{{ asset('assets/css/orange.css') }}" id="theme-color">
    <style>
        .logo {
            height: 55px;
            width: auto
        }

        .search-form {
            display: flex;
            align-items: center;
            margin-right: 3rem;
        }

        .search-input {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            padding: 8px 12px;
            width: 250px;
        }

        .search-btn {
            background-color: #fd7014;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-btn:hover {
            background-color: whitesmoke;
        }



        @media (max-width: 768px) {
            .navbar-nav {
                margin-top: 10px;
                padding-top: 0.5rem;
                background-color: black;
                align-items: center;
                line-height: 2.5;
                border: 2px solid orange !important;
                border-radius: 10px;
            }

            .input-group {
                width: 86%;
            }
        }
    </style>
</head>

<body>

    <!--navigation-->
    <section class="smart-scroll">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-md navbar-dark">
                <a class="navbar-brand heading-black" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/logo2.png') }}" alt="Logo" class="logo">
                </a>
                <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span data-feather="grid"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link page-scroll" href="{{ url('/text') }}">AI Writing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link page-scroll" href="{{ url('/image') }}">AI Image Analysis</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle page-scroll" href="#" id="navbarDropdownPDF"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Files
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownPDF">
                                <a class="dropdown-item" href="{{ url('/upload') }}">PDF to Word</a>
                                <a class="dropdown-item" href="{{ url('/pdf-to-text') }}">PDF to Text</a>
                                <a class="dropdown-item" href="{{ url('/word-to-pdf') }}">Word to PDF</a>

                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle page-scroll" href="#" id="navbarDropdownPDF"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                More
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownPDF">
                                <a class="dropdown-item" href="{{ url('/url-shortener') }}">Shorten URL</a>
                                <a class="dropdown-item" href="{{ url('/qr-code') }}">QR Maker</a>
                                <a class="dropdown-item" href="{{ url('/barcode') }}">Barcode Maker</a>

                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link page-scroll d-flex flex-row align-items-center text-primary"
                                href="{{ url('/all-tools') }}">
                                <em data-feather="settings" width="18" height="18" class="mr-2"></em>
                                Try All Tools
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>
        </div>
    </section>


    {{-- Main Body --}}
    @yield('content')

    <!--footer-->
    <footer class="py-4" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-5 mr-auto">
                    <h5>About ContentCraft</h5>
                    <p class="text-muted">We provide a library of handy tools to help you solve problems of all types.
                        All files are deleted right after processing.</p>
                    <ul class="list-inline social social-sm">
                        <li class="list-inline-item">
                            <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#"><i class="fa-brands fa-google-plus"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-2">
                    <h5>Navigate</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/') }}#about">About</a></li>
                        <li><a href="{{ url('/all-tools') }}">Tools</a></li>
                    </ul>
                </div>
                <div class="col-sm-2">
                    <h5>AI Tools</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/text') }}">Text Generator</a></li>
                        <li><a href="{{ url('/image') }}">Image Analysis</a></li>
                    </ul>
                </div>
                <div class="col-sm-2">
                    <h5>Other Tools</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/upload') }}">PDF to Word</a></li>
                        <li><a href="{{ url('/qr-code') }}">QR Code Maker</a></li>
                        <li><a href="{{ url('/all-tools') }}">More tools</a></li>
                    </ul>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12 text-muted text-center small-xl">
                    &copy; 2024 ContentCraft - All Rights Reserved
                </div>
            </div>
        </div>
    </footer>

    <!--scroll to top-->
    <div class="scroll-top">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
    </div>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.7.3/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
</body>

</html>
