<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Services\PdfTemplatePrinterService;
use Illuminate\Http\Request;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

class PrintPdfController extends Controller
{
    public function edit($pdfTemplateId)
    {
        $pdfTemplate = PdfTemplate::query()->findorFail($pdfTemplateId);

        return view('print-pdfs.edit', [
            'pdfTemplate' => $pdfTemplate->load('fields'),
        ]);
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    public function update($pdfTemplate, Request $request, PdfTemplatePrinterService $printer)
    {
        $pdfTemplate = PdfTemplate::query()->findOrFail($pdfTemplate);

        $request->validate([
            'filename' => 'required|string',
            'data' => 'required|array',
            'data.*.value' => ['required', 'string'],
            'data.*.x' => ['required', 'int', 'min:0'],
            'data.*.y' => ['required', 'int', 'min:0'],
        ]);

        if ($request->input('action') === 'save') {
            $pdfTemplate->fields()->delete(); // clear old layout

            foreach ($request->data as $item) {
                $pdfTemplate->fields()->create([
                    'value' => $item['value'],
                    'x' => $item['x'],
                    'y' => $item['y'],
                ]);
            }

            return back()->with('success', 'Layout saved successfully.');
        }

        $pdfPath = storage_path("app/public/{$request->filename}");
        $outputPath = storage_path("app/public/data_{$request->filename}");

        if (! file_exists($pdfPath)) {
            return back()->with('error', 'PDF file not found.');
        }

        $variables = [
            'DATE' => now()->format('Y-m-d'),
            'INVOICE_NUMBER' => '#123141',
            'INVOICE_TO' => '#9874621',
            'SHIP_TO' => 'Some shipment company',
            'SUB_TOTAL' => 300,
            'GST_TOTAL' => 150,
            'TOTAL' => 569,
            'QUANTITY' => [
                10,
                20,
                30,
            ],
            'DESCRIPTION' => [
                'Desc 10',
                'Desc 20',
                'Desc 30',
            ],
        ];

        $printer->generate($pdfPath, $outputPath, $request->data, $variables);

        return response()->file($outputPath);
    }
}
