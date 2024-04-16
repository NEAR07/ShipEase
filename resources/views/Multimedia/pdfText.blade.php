@extends('layouts.app')
<title>PDF to Text</title>
@section('content')
    <div class="container mt-7 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="text-center">Extract Text from PDF</h2>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('pdf.to.text.extract') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="inputFile" class="form-label">Upload PDF File:</label>
                                <input type="file" name="pdf" id="inputFile" class="form-control">
                            </div>

                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-success">Extract</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
