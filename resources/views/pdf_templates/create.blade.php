@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload PDF</h2>

        <form action="{{ route('pdf-templates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="pdf">Select PDF File:</label>
                <input type="file" name="pdf" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success mt-2">Upload PDF & Generate Grid</button>
        </form>
    </div>
@endsection
