@extends('layouts.app')
<title>AI Text Generator</title>
@section('content')
    <style>
        body {
            background-image: url("{{ asset('assets/img/bg4.jpeg') }}");
            background-size: cover;
        }

        #prompt {
            margin-bottom: 10px;
        }

        #loader {
            display: none;
            height: 20px;
            margin-bottom: 10px;
        }

        #result {
            resize: none;
        }

        .down {
            font-size: 17px;
        }

        textarea {
            width: 100%;
            height: 300px;
            box-sizing: border-box;
            margin-top: 30px;
        }

        #copy:hover {
            background-color: #fd7014;
            border-color: #fd7014;
        }
    </style>
    <div class="container mt-6 mb-5">
        <div class="row">
            <!-- Guideline box column -->
            <div class="col-md-3">
                <div class="guideline-box">
                    <h4 class="guideline-title text-center">Steps to follow</h4>
                    <ul class="list-unstyled">
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Enter your text in the first
                            field.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Click the "Generate" button.
                        </li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> View the result in the
                            textarea.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Click the "Copy" button to
                            copy the result.</li>
                    </ul>
                </div>
            </div>

            <!-- Main content column -->
            <div class="col-md-9">
                <div class="position-relative">
                    <h3>Start Typing Here <i class="fa-solid fa-arrow-turn-down down"></i></h3>
                    <input id="prompt" style="color: #2d2d2d" class="form-control" type="text"
                        placeholder="What's on your mind today?">
                    <button id="generate" class="btn btn-primary mt-2"><i class="fa-solid fa-gear"></i> Generate</button>
                    <button id="copy" class="btn btn-secondary mt-2 float-right" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Copy to clipboard">
                        📋
                    </button>
                    <div id="loader"></div>
                    <textarea id="result" class="form-control mt-2" style="color: #2d2d2d" readonly></textarea>
                </div>
            </div>
        </div>
    </div>

    <script type="importmap">
    {
      "imports": {
        "@google/generative-ai": "https://esm.run/@google/generative-ai"
      }
    }
  </script>
    <script type="module">
        async function fetchApiKey() {
            try {
                const response = await fetch("http://localhost:3000/config");
                if (!response.ok) {
                    throw new Error(`Error: ${response.statusText}`);
                }
                const {
                    API_KEY
                } = await response.json();
                return API_KEY;
            } catch (error) {
                console.error("Error fetching API key:", error.message);
                throw error;
            }
        }

        async function runModel() {
            try {
                const loader = document.getElementById("loader");
                const resultTextarea = document.getElementById("result");

                loader.style.display = "block";
                resultTextarea.value = "Generating...";

                const API_KEY = await fetchApiKey();

                const prompt = document.getElementById("prompt").value;
                const response = await fetch(
                    `http://localhost:3000/generateContent/${prompt}`
                );
                const text = await response.text();

                resultTextarea.value = text;
            } catch (error) {
                console.error("Error during generation:", error.message);
                document.getElementById("result").value =
                    "Error during generation. Please try again.";
            } finally {
                loader.style.display = "none";
            }
        }


        document.getElementById("generate").addEventListener("click", runModel);

        document.getElementById("prompt").addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                runModel();
            }
        });

        document.getElementById("copy").addEventListener("click", () => {
            const textarea = document.getElementById("result");
            textarea.select();
            document.execCommand("copy");
        });
    </script>
@endsection
