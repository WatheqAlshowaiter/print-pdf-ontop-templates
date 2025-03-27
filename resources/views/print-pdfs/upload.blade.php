@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Upload a PDF</h2>
        <form action="{{ route('pdf.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="pdf" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload PDF</button>
        </form>
    </div>
@endsection
