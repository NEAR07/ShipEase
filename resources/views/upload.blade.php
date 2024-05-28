<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PDF to DOCX Converter</title>
</head>
<body>
    <h2>PDF to DOCX Converter</h2>
    <form id="uploadForm" enctype="multipart/form-data" method="POST" action="/convert">
        @csrf
        <label for="file">Choose a PDF file:</label>
        <input type="file" id="file" name="file" accept=".pdf" required>
        <button type="submit">Convert</button>
    </form>
    <div id="message"></div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('/convert', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('message').innerText = data.message;
                if (data.outputFile) {
                    const link = document.createElement('a');
                    link.href = `/storage/${data.outputFile}`;
                    link.innerText = 'Download Converted File';
                    document.getElementById('message').appendChild(link);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message').innerText = 'Error occurred during file conversion.';
            });
        });
    </script>
</body>
</html>
