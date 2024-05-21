@extends('layouts.app')
<title>PDF to DOCX Converter</title>
@section('content')

    <body>
        <h2>PDF to DOCX Converter</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <label for="file">Choose a PDF file:</label>
            <input type="file" id="file" name="file" accept=".pdf">
            <button type="submit">Convert</button>
        </form>
        <div id="message"></div>

        <script>
            document.getElementById('uploadForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData();
                formData.append('file', document.getElementById('file').files[0]);

                fetch('/convert', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('message').innerText = data.message;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('message').innerText = 'Error occurred during file conversion.';
                    });
            });
        </script>
    </body>
@endsection
