@extends('layouts.app')
<title>AI Image-to-Text</title>
@section('content')
    <style>
        #loader {
            display: none;
            margin-top: 10px;
        }
    </style>
    <div class="container mt-5">
        <div class="row justify-content-center py-6">
            <div class="col-md-6">
                <form id="imageForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="imageInput" class="form-label">Upload an image:</label>
                        <input type="file" accept="image/*" class="form-control" id="imageInput" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="generateContent()"><i
                            class="fa-solid fa-gear"></i> Generate</button>
                </form>

                <div id="loader" class="alert alert-info" role="alert">
                    Processing...
                </div>

                <div id="feedback"></div>
            </div>
        </div>
    </div>

    <script>
        async function generateContent() {
            const imageInput = document.getElementById("imageInput");
            const file = imageInput.files[0];

            if (!file) {
                alert("Please select an image.");
                return;
            }

            const loader = document.getElementById("loader");
            const feedback = document.getElementById("feedback");

            loader.style.display = "block";
            feedback.textContent = "";

            const formData = new FormData();
            formData.append("image", file);

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
