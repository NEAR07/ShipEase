@extends('layouts.app')
@section('content')
    <style>
        body {
            background-color: #0B0B0B;
        }

        .bg-hero {
            background-image: url("{{ asset('assets/img/hero.jpg') }}");
        }

        .section-angle {
            border-color: #1de9b6;
        }

        .card {
            background-color: #2d2d2d;
        }
    </style>
    <!--hero header-->
    <section class="py-7 py-md-0 bg-hero" id="home">
        <div class="container">
            <div class="row vh-md-100 hero">
                <div class="col-md-8 col-sm-10 col-12 mx-auto my-auto text-center">
                    <h1 class="heading-black text-capitalize">The Only Limit Is Your Imagination</h1>
                    <p class="lead py-3">Free. Easy. No Signup Required</p>
                    <a href="{{ url('/text') }}">
                        <button class="btn btn-primary d-inline-flex flex-row align-items-center">
                            Check our new AI
                            <em class="ml-2" data-feather="arrow-right"></em>
                        </button></a>
                </div>
            </div>
        </div>
    </section>

    <!-- features section -->
    <section class="pt-6 pb-7" id="features">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto text-center">
                    <h2 class="heading-black">Knight offers everything you need.</h2>
                    <p class="text-muted lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum in
                        nisi
                        commodo, tempus odio a, vestibulum nibh.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-10 mx-auto">
                    <div class="row feature-boxes">
                        <div class="col-md-6 box">
                            <div class="icon-box box-primary">
                                <div class="icon-box-inner">
                                    <span data-feather="edit-3" width="35" height="35"></span>
                                </div>
                            </div>
                            <h5>Create once. Share everywhere.</h5>
                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum in
                                nisi commodo, tempus odio a, vestibulum nibh.</p>
                        </div>
                        <div class="col-md-6 box">
                            <div class="icon-box box-success">
                                <div class="icon-box-inner">
                                    <span data-feather="monitor" width="35" height="35"></span>
                                </div>
                            </div>
                            <h5>Unlimited devices</h5>
                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum in
                                nisi commodo, tempus odio a, vestibulum nibh.</p>
                        </div>
                        <div class="col-md-6 box">
                            <div class="icon-box box-danger">
                                <div class="icon-box-inner">
                                    <span data-feather="layout" width="35" height="35"></span>
                                </div>
                            </div>
                            <h5>Beautiful tempates & layouts</h5>
                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum in
                                nisi commodo, tempus odio a, vestibulum nibh.</p>
                        </div>
                        <div class="col-md-6 box">
                            <div class="icon-box box-info">
                                <div class="icon-box-inner">
                                    <span data-feather="globe" width="35" height="35"></span>
                                </div>
                            </div>
                            <h5>Available globally</h5>
                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum in
                                nisi commodo, tempus odio a, vestibulum nibh.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--news section-->
    <section class="py-7 bg-dark" id="blog">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto text-center">
                    <h2 class="heading-black">News from Knight.</h2>
                    <p class="text-muted lead">What's new at Knight.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card">
                        <a href="#">
                            <img class="card-img-top img-raised" src="img/blog-1.jpg" alt="Blog 1">
                        </a>
                        <div class="card-body">
                            <a href="#" class="card-title mb-2">
                                <h5>We launch new iOS & Android mobile apps</h5>
                            </a>
                            <p class="text-muted small-xl mb-2">Sep 27, 2018</p>
                            <p class="card-text">Nam liber tempor cum soluta nobis eleifend option congue nihil imper,
                                consectetur adipiscing elit. <a href="#">Learn more</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <a href="#">
                            <img class="card-img-top img-raised" src="img/blog-2.jpg" alt="Blog 2">
                        </a>
                        <div class="card-body">
                            <a href="#" class="card-title mb-2">
                                <h5>New update is available for the editor</h5>
                            </a>
                            <p class="text-muted small-xl mb-2">August 16, 2018</p>
                            <p class="card-text">Nam liber tempor cum soluta nobis eleifend option congue nihil imper,
                                consectetur adipiscing elit. <a href="#">Learn more</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <a href="#">
                            <img class="card-img-top img-raised" src="img/blog-3.jpg" alt="Blog 3">
                        </a>
                        <div class="card-body">
                            <a href="#" class="card-title mb-2">
                                <h5>The story of building #1 page builder</h5>
                            </a>
                            <p class="text-muted small-xl mb-2">December 2nd, 2017</p>
                            <p class="card-text">Nam liber tempor cum soluta nobis eleifend option congue nihil imper,
                                consectetur adipiscing elit. <a href="#">Learn more</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-6">
                <div class="col-md-6 mx-auto text-center">
                    <a href="#" class="btn btn-primary">View all posts</a>
                </div>
            </div>
        </div>
    </section>

    <!--faq section-->
    <section class="py-7 section-angle top-left bottom-left" id="faq">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto text-center">
                    <h2>Frequently asked questions</h2>
                    <p class="text-muted lead">Answers to most common questions.</p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-10 mx-auto">
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <h6>Can I try it for free?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                        <div class="col-md-6 mb-5">
                            <h6>Do you have hidden fees?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                        <div class="col-md-6 mb-5">
                            <h6>What are the payment methods you accept?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                        <div class="col-md-6 mb-5">
                            <h6>How often do you release updates?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                        <div class="col-md-6 mb-5">
                            <h6>What is your refund policy?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                        <div class="col-md-6 mb-5">
                            <h6>How can I contact you?</h6>
                            <p class="text-muted">Nam liber tempor cum soluta nobis eleifend option congue nihil imper
                                per tem por legere me doming.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6 mx-auto text-center">
                    <h5 class="mb-4">Have questions?</h5>
                    <a href="#" class="btn btn-primary">Contact us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
