<!DOCTYPE html>
<html>

<head>
    <title>PDF to Word Converter</title>
</head>

<body>
    <form action="/convert" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="pdf">
        <button type="submit">Convert</button>
    </form>
</body>

</html>
