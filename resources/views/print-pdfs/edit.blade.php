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
                @php
                    $fields = old('data') ?? $pdfTemplate->fields;
                @endphp

                @foreach($fields as $index => $entry)
                    <div class="row mb-2">
                        <div class="col">
                            <select name="data[{{ $index }}][value]" class="form-control @error("data.$index.value") is-invalid @enderror">
                                <option value="" disabled selected>Select Label</option>
                                @foreach (\App\Enums\PdfValues::cases() as $label)
                                    <option value="{{ $label->name }}"
                                    @if(old("data.$index.value"))
                                        {{ old("data.$index.value") == $label->name ? 'selected' : '' }}
                                        @else
                                        {{ isset($entry->value) && $entry->value === $label->name ? 'selected' : '' }}
                                        @endif
                                    >
                                        {{ $label->value }}
                                    </option>
                                @endforeach
                            </select>
                            @error("data.$index.value")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <input type="number" name="data[{{ $index }}][x]"
                                   value="{{ old("data.$index.x") ?? ($entry->x ?? '') }}"
                                   placeholder="X Position"
                                   class="form-control @error("data.$index.x") is-invalid @enderror">
                            @error("data.$index.x")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <input type="number" name="data[{{ $index }}][y]"
                                   value="{{ old("data.$index.y") ?? ($entry->y ?? '') }}"
                                   placeholder="Y Position"
                                   class="form-control @error("data.$index.y") is-invalid @enderror">
                            @error("data.$index.y")
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger remove-field">X</button>
                        </div>
                    </div>
                @endforeach
            </div>


            <button type="button" onclick="addField()" class="btn btn-secondary">+ Add More</button>
            <button type="submit" name="action" value="generate" class="btn btn-success">Generate PDF</button>
            <button type="submit" name="action" value="save" class="btn btn-primary">Save Layout</button>
        </form>

        <br/>

        <embed src="{{ asset('storage/' . $pdfTemplate->grid_pdf_path) }}" type="application/pdf" width="100%"
               height="600px">

    </div>

    <script>
        @php
            $labelOptions = collect(\App\Enums\PdfValues::cases())->map(fn($label) => [
                'value' => $label->value,
                'name' => $label->name
            ]);
        @endphp

        let index = document.querySelectorAll('#data-fields .row').length;
        const labelOptions = @json($labelOptions);

        function addField() {
            let container = document.getElementById('data-fields');
            let div = document.createElement('div');
            div.className = "row mb-2";
            let optionsHtml = `<option value="" disabled selected>Select Label</option>`;
            labelOptions.forEach(option => {
                optionsHtml += `<option value="${option.name}">${option.value}</option>`;
            });

            div.innerHTML = `
                <div class="col">
                <select name="data[${index}][value]" class="form-control">
                    ${optionsHtml}
                </select>
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
