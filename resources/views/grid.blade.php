@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Map PDF to Grid</h2>

        <embed src="{{ asset('storage/' . $template->pdf_path) }}" type="application/pdf" width="100%" height="600px">

        <p>Click on the PDF to place dynamic data fields.</p>
    </div>
@endsection
