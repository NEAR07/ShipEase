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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = document.getElementById('file').files[0].name.replace('.pdf', '.docx');
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.getElementById('message').innerText =
                    'File converted successfully and downloaded.';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('message').innerText = 'Error occurred during file conversion.';
                });
        });
    </script>
</body>

</html>
