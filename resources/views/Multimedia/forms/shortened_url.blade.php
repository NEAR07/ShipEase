@extends('layouts.app')
<title>Shorten URL</title>
@section('content')
    <style>
        body {
            background-image: url("{{ asset('assets/img/bg2.jpeg') }}");
            background-size: cover;
        }
    </style>
    <div class="container mt-4">
        <div class="row justify-content-center py-6">
            <!-- Guideline box column -->
            <div class="col-md-3">
                <div class="guideline-box">
                    <h4 class="guideline-title text-center">Steps to follow</h4>
                    <ul class="list-unstyled">
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Enter the full URL you want to
                            shorten.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Shorten Link"
                            button.
                        </li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> Wait for the shortened URL to
                            be generated.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Copy and use the shortened
                            URL as needed.</li>
                    </ul>
                </div>
            </div>

            <!-- URL Shortening form column -->
            <div class="col-md-6">
                <div class="panel-heading mt-5">
                    <h2 class="text-center">URL Shortener</h2>
                </div>
                @if (!isset($url))
                    <form id="shorten-url-form" class="mt-4" action="{{ route('url.shorten') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="url" class="form-label" style="font-weight: bold;">Enter your URL</label>
                            <div class="input-group">
                                <input class="form-control" type="url" name="url" id="url"
                                    placeholder="https://example.com" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary generate-button" id="generateURLButton" type="submit"
                                        style="background-color: #fd7014">Shorten Link <i
                                            class="fa-solid fa-link"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if (isset($url) && isset($shortenedURL))
                    <div id="shortened-url-container" class="mt-7">
                        <p style="font-weight: bold">Original URL: {{ $url }}</p>
                        <p style="font-weight: bold">Shortened URL: <a href="{{ $shortenedURL }}" style="color: #fd7014"
                                target="_blank">{{ $shortenedURL }}</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
