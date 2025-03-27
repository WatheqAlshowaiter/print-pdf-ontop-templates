@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>View PDF</h2>

        @if($grid)
           <embed src="{{ asset('storage/' . $template->grid_pdf_path) }}" type="application/pdf" width="100%" height="600px">
        @else
            <embed src="{{ asset('storage/' . $template->pdf_path) }}" type="application/pdf" width="100%" height="600px">
        @endif
        <a href="{{ route('pdf-templates.index') }}" class="btn btn-secondary mt-2">Back to List</a>
    </div>
@endsection
