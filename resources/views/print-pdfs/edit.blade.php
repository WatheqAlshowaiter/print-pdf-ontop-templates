@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit PDF: {{ $pdfTemplate->name }} <span class="small text-bg-secondary">#({{ $pdfTemplate->ulid }})</span>
        </h2>

        <!-- Form to input data for PDF -->
        <form action="{{ route('print-pdfs.update', $pdfTemplate->id) }}" method="POST">
            @method('PUT')
            @csrf

            <input type="hidden" name="filename" value="{{ $pdfTemplate->pdf_path }}">

            <div id="data-fields">
                @if(old('data'))
                    @foreach(old('data') as $index => $entry)
                        <div class="row mb-2">
                            <div class="col">
                                <input type="text" name="data[{{ $index }}][value]" placeholder="Label"
                                       class="form-control @error("data.$index.value") is-invalid @enderror"
                                       value="{{ old("data.$index.value") }}">
                                @error("data.$index.value")
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <input type="number" name="data[{{ $index }}][x]" placeholder="X Position"
                                       class="form-control @error("data.$index.x") is-invalid @enderror"
                                       value="{{ old("data.$index.x") }}">
                                @error("data.$index.x")
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <input type="number" name="data[{{ $index }}][y]" placeholder="Y Position"
                                       class="form-control @error("data.$index.y") is-invalid @enderror"
                                       value="{{ old("data.$index.y") }}">
                                @error("data.$index.y")
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-danger remove-field">X</button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" name="data[0][value]" placeholder="Label" class="form-control">
                        </div>
                        <div class="col">
                            <input type="number" name="data[0][x]" placeholder="X Position" class="form-control">
                        </div>
                        <div class="col">
                            <input type="number" name="data[0][y]" placeholder="Y Position" class="form-control">
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger remove-field">X</button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" onclick="addField()" class="btn btn-secondary">+ Add More</button>
            <button type="submit" class="btn btn-success">Generate PDF</button>
        </form>

        <br/>

        <embed src="{{ asset('storage/' . $pdfTemplate->grid_pdf_path) }}" type="application/pdf" width="100%"
               height="600px">

    </div>

    <script>
        let index = document.querySelectorAll('#data-fields .row').length;

        function addField() {
            let container = document.getElementById('data-fields');
            let div = document.createElement('div');
            div.className = "row mb-2";
            div.innerHTML = `
                <div class="col">
                    <input type="text" name="data[${index}][value]" placeholder="Label" class="form-control">
                </div>
                <div class="col">
                    <input type="number" name="data[${index}][x]" placeholder="X Position" class="form-control">
                </div>
                <div class="col">
                    <input type="number" name="data[${index}][y]" placeholder="Y Position" class="form-control">
                </div>
                <div class="col">
                    <button type="button" class="btn btn-danger remove-field">X</button>
                </div>
            `;
            container.appendChild(div);
            index++;
        }

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-field')) {
                event.target.closest('.row').remove();
            }
        });
    </script>
@endsection
