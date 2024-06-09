@extends('layouts.app')
<title>AI Image-to-Text</title>
@section('content')
    <style>
        body {
            background-image: url("{{ asset('assets/img/bg4.jpeg') }}");
            background-size: cover;
        }

        #loader {
            display: none;
            height: 20px;
            margin-top: 10px;
        }
    </style>
    <div class="container mt-5">
        <div class="row justify-content-center py-6">
            <!-- Guideline box column -->
            <div class="col-md-3">
                <div class="guideline-box" style="margin-top:32px">
                    <h4 class="guideline-title text-center">Steps to follow</h4>
                    <ul class="list-unstyled">
                        <li class="guideline-step"><span style="font-weight: bolder">1.</span> Click "Browse" and select an
                            image.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">2.</span> Type your prompt in the field
                            below.</li>
                        <li class="guideline-step"><span style="font-weight: bolder">3.</span> Click the "Generate" button.
                        </li>
                        <li class="guideline-step"><span style="font-weight: bolder">4.</span> Wait for the processing and
                            see the result.</li>
                    </ul>
                </div>
            </div>

            <!-- Form and feedback column -->
            <div class="col-md-6">
                <form id="imageForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="imageInput" class="form-label" style="font-weight: bold">Upload an image:</label>
                        <input type="file" accept="image/*" class="form-control" id="imageInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="promptInput" class="form-label" style="font-weight: bold">Enter your prompt:</label>
                        <input type="text" class="form-control" id="promptInput" name="prompt"
                            placeholder="e.g., Describe this image" style="color: #2d2d2d" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="generateContent()">
                        <i class="fa-solid fa-gear"></i> Generate
                    </button>
                </form>

                <div id="loader" class="mt-3" style="font-weight: bold">
                    Processing...
                </div>

                <div id="feedback" class="mt-3" style="font-weight: bold"></div>
            </div>
        </div>
    </div>

    <script>
        async function generateContent() {
            const imageInput = document.getElementById("imageInput");
            const file = imageInput.files[0];
            const promptInput = document.getElementById("promptInput").value.trim(); // Trim whitespace

            if (!file) {
                alert("Please select an image.");
                return;
            }

            if (!promptInput) { // Check if prompt is empty
                alert("Please enter a prompt.");
                return;
            }

            const loader = document.getElementById("loader");
            const feedback = document.getElementById("feedback");

            loader.style.display = "block";
            feedback.textContent = "";

            const formData = new FormData();
            formData.append("image", file);
            formData.append("prompt", promptInput); // Include user's entered prompt

            try {
                const response = await fetch(
                    "http://localhost:3000/generateContent", {
                        method: "POST",
                        body: formData,
                    }
                );

                // Check if response status is OK (200)
                if (!response.ok) {
                    throw new Error(
                        `Error: ${response.status} - ${response.statusText}`
                    );
                }

                // Parse response JSON
                const data = await response.json();

                if (data.success) {
                    feedback.textContent = data.description;
                } else {
                    alert("Error generating content");
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Error generating content");
            } finally {
                loader.style.display = "none";
            }
        }
    </script>
@endsection
