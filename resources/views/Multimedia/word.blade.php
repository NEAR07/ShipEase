@extends('layouts.app')
<title>Word to Pdf</title>
@section('content')
    <div class="container mt-7 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="text-center">Word to PDF File Convert</h2>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('word.to.pdf.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="inputFile" class="form-label">Upload Word File:</label>
                                <input type="file" name="file" id="inputFile" class="form-control">
                            </div>

                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-success">Convert</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
