@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Uploaded PDFs</h2>
        <a href="{{ route('pdf-templates.create') }}" class="btn btn-primary">Upload New PDF</a>

        <table class="table mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>PDF File</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pdfTemplates as $template)
                <tr>
                    <td>{{ $template->id }}</td>
                    <td>{{ $template->name }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $template->grid_pdf_path) }}" target="_blank">View GRID PDF</a>
                        &nbsp;
                        &nbsp;
                        <a href="{{ asset('storage/' . $template->pdf_path) }}" target="_blank">View PDF</a>
                    </td>
                    <td>
                        <form  style="display: inline;" action="{{ route('pdf-templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                        <a href="{{ route('pdf-templates.show', ['pdf_template' => $template->id, 'grid' => true]) }}" class="btn btn-info btn-sm text-white">View GRID</a>
                        <a href="{{ route('pdf-templates.show', $template->id) }}" class="btn btn-warning btn-sm text-white">View</a>
                        <a href="{{ route('print-pdfs.edit', $template) }}" class="btn btn-dark btn-sm text-white">Add Data</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
