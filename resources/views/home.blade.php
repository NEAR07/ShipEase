@extends('layouts.app')
@section('content')
    <style>
        body {
            background-color: #0B0B0B;
        }

        .bg-hero {
            background-image: url("{{ asset('assets/img/bgsh.jpg') }}");
        }

        #features {
            background-image: url("{{ asset('assets/img/bg2.jpeg') }}");
            background-size: cover;
            padding: 60px 0;
        }

        #premium {
            background-image: url("{{ asset('assets/img/bg3.jpeg') }}");
            background-size: cover;
            background-color: rgba(16, 14, 14, 0.5);
        }

        #about {
            background-image: url("{{ asset('assets/img/bg1.jpeg') }}");
            background-size: cover;
            background-color: rgba(16, 14, 14, 0.5);
        }

        .bg-dark {
            background-color: rgba(255, 145, 0, 0.1) !important
        }

        .section-angle {
            border-color: #fd7014;
        }

        .card {
            background-color: #2d2d2d;
        }

        .product-card {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .product-name {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 16px;
            color: #666;
        }

        i.premium {
            color: #ea782f;
        }

        /* Expanding Cards CSS */
        @import url('https://fonts.googleapis.com/css2?family=Lobster&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-color: #222;
            --border-color: #14133f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .features-container {
            max-width: 1700px;
            margin: 0 auto;
            text-align: center;
        }

        .features-title {
            text-align: center;
        }

        .features-title h1 {
            /* font-family: "Montserrat", sans-serif; */
            font-size: clamp(2rem, 5vw, 4rem); 
            font-weight: 700; 
            color: #fff;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .features-title p {
        /* font-family: "Montserrat", sans-serif; */
        font-size: clamp(1rem, 2vw, 1.25rem);
        font-weight: 300;
        color: #fff;
        margin-bottom: 40px;
        line-height: 1;
        margin-left: auto;
        margin-right: auto;
        white-space: nowrap; 
    }

        .card-container {
            display: flex;
            height: 70vh;
            overflow: hidden;
            border-radius: 20px;
            width: 100%; 
        }

        .card {
            position: relative;
            flex: 1; 
            min-width: 100px; 
            z-index: 1;
            transition: flex 1.5s ease-in-out 0.1s, z-index 0.3s;
            cursor: pointer;
            /* overflow: hidden; */
            background-color: transparent;
        }

        .card:hover {
            flex: 5; 
            z-index: 2;
        }

        .inner {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .inner h2 {
        position: absolute;
        top: 50%; /* Posisi vertikal di tengah */
        left: 50%; /* Posisi horizontal di tengah */
        transform: translate(-50%, -50%); /* Memastikan benar-benar di tengah */
        color: #fff; /* Warna putih */
        font-family: "Open Sans", sans-serif;
        font-size: 2rem; /* Diperkecil dari 20rem ke 10rem */
        text-align: left;
        transition: 0.5s;
        z-index: 1;
    }

        .card:hover .inner h2 {
            text-shadow: 1px 0 15px rgb(232, 229, 229);
            transition: 0.5s;
        }

        .card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            filter: grayscale(0.9);
            transition: transform 2s cubic-bezier(0.47, 0, 0.745, 0.715), filter 0.6s;
            transform-origin: center;
        }

        .card:hover img {
            transform: scale(1.1);
            filter: grayscale(0);
        }

        .overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: start;
            align-items: end;
            padding: 20px;
            background: linear-gradient(
                180deg,
                transparent,
                transparent,
                rgba(0, 0, 0, 0.2),
                rgba(0, 0, 0, 0.6)
            );
            opacity: 0;
            transition: opacity 0.6s ease-in-out 0.3s;
            pointer-events: none;
            overflow: hidden;
        }

        .card:hover .overlay {
            opacity: 1;
            transition: opacity 1.3s ease-in-out 0.7s;
        }

        .overlay h3 {
            font-family: "Open Sans", sans-serif;
            color: #fff;
            font-size: clamp(1rem, 3vw, 2rem);
            text-align: left;
            font-weight: 300;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
        }

        .card:hover .overlay h3 {
            opacity: 1;
            transition: opacity 1s ease-in;
        }

        @media (max-width: 1200px) {
            .inner h2 {
                font-size: 16rem;
            }
        }

        @media (max-width: 950px) {
            .inner h2 {
                top: 5%;
                font-size: 14rem;
            }
        }

        @media (max-width: 850px) {
            .features-title h1 {
                width: max-content;
                margin: 0 auto 30px;
            }
        }

        @media (max-width: 620px) {
            .inner h2 {
                top: 10%;
                left: 10%;
                font-size: 10rem;
            }
        }

        @media (max-width: 500px) {
            .card-container {
                flex-direction: column;
            }

            .inner h2 {
                top: -10px;
                left: 10%;
                font-size: 7rem;
            }

            .card {
                min-width: 0; /* Hilangkan min-width di layar kecil */
            }

            .card:hover {
                flex: 1 1 80vh;
            }
        }
    </style>

    <!--hero header-->
    <section class="py-7 py-md-0 bg-hero" id="home">
        <div class="container">
            <div class="row vh-md-100 hero">
                <div class="col-md-8 col-sm-10 col-12 mx-auto my-auto text-center">
                    <h1 class="heading-black text-capitalize">The Only Limit Is Your Imagination</h1>
                    <p class="lead py-3">Free. Easy. No Signup Required</p>
                    <a href="{{ url('/all-tools') }}">
                        <button class="btn btn-primary d-inline-flex flex-row align-items-center">
                            Get Started
                            <em class="ml-2" data-feather="arrow-right"></em>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- features section -->
    <section id="features">
        <div class="features-container">
        <div class="features-title">
            <h1>Future-leading Technologies</h1>
            <p>We create value through the development and production of innovative products, the development of cutting-edge technology, and endless challenges.</p>
        </div>
            <div class="card-container">
                <div class="card">
                    <div class="inner">
                        <img src="{{ asset('assets/img/image1.jpg') }}" alt="Continuous Product Innovation and Empowering Engineering Methode Through Efficiency" />
                        <h2>Our Improvement</h2>
                        <div class="overlay">
                            <h3>Continuous Product Innovation and Empowering Engineering Methode Through Efficiency</h3>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="inner">
                        <img src="{{ asset('assets/img/image2.jpg') }}" alt="Leveraging Artificial Intelligence to Redefine Possibilities" />
                        <h2>AI-Driven Innovation</h2>
                        <div class="overlay">
                            <h3>Leveraging Artificial Intelligence to Redefine Possibilities</h3>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="inner">
                        <img src="{{ asset('assets/img/image3.jpg') }}" alt="Revolutionizing Marine Operations with Smart Technologies" />
                        <h2>Maritime Industry 4.0</h2>
                        <div class="overlay">
                            <h3>Revolutionizing Marine Operations with Smart Technologies</h3>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="inner">
                        <img src="{{ asset('assets/img/image4.jpg') }}" alt="Accelerating Growth Through Advanced Data Analytics and Automation" />
                        <h2>Digital Transformation</h2>
                        <div class="overlay">
                            <h3>Accelerating Growth Through Advanced Data Analytics and Automation</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Premium section-->
    <section class="py-3 section-angle bottom-left" id="premium">
        <div class="container overlay">
        </div>
    </section>

    <!--About section-->
    <section class="pb-6 pt-7" id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-7 mx-auto text-center">
                    <h2 class="text-primary">What ShipEase offers?</h2>
                    <p class="text-muted lead">Tools to make your life easier.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-10 mx-auto">
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <div class="media">
                                <div class="icon-box">
                                    <div class="icon-box-inner small-xs text-primary">
                                        <span data-feather="zap" width="30" height="30"></span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h5 class="mt-3 mb-1">Quick solution</h5>
                                    Do the tedious tasks fast and easy.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="media">
                                <div class="icon-box">
                                    <div class="icon-box-inner small-xs text-primary">
                                        <span data-feather="shield" width="30" height="30"></span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h5 class="mt-3 mb-1">Secure environment</h5>
                                    Your data is only yours.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="media">
                                <div class="icon-box">
                                    <div class="icon-box-inner small-xs text-primary">
                                        <span data-feather="clock" width="30" height="30"></span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h5 class="mt-3 mb-1">24/7 availability</h5>
                                    Anytime. Anywhere
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="media">
                                <div class="icon-box">
                                    <div class="icon-box-inner small-xs text-primary">
                                        <span data-feather="settings" width="30" height="30"></span>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h5 class="mt-3 mb-1">More additions</h5>
                                    Routinely adding more tools.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
</script>
@endsection