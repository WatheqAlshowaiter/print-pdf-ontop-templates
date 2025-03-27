<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;

class PrintPdfController extends Controller
{
    // Show upload form
    public function showUploadForm()
    {
        return view('pdf.upload');
    }

    // Handle PDF upload
    public function uploadPDF(Request $request)
    {
        $request->validate(['pdf' => 'required|mimes:pdf|max:10240']); // Max 10MB
        $filename = Carbon::now()->getTimestamp().'.'.$request->pdf->extension();
        $request->pdf->storeAs('public', $filename);

        return redirect()->route('pdf.edit.form', ['filename' => $filename]);
    }

    // Show edit form with input fields
    public function edit($pdfTemplateId)
    {
        $pdfTemplate = PdfTemplate::query()->findorFail($pdfTemplateId);

        return view('print-pdfs.edit', ['pdfTemplate' => $pdfTemplate]);
    }

    // Add data to PDF

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    public function update($pdfTemplate, Request $request)
    {
        PdfTemplate::query()->findorFail($pdfTemplate);

        $request->validate([
            'filename' => 'required|string',
            'data' => 'required|array',
            'data.*.value' => ['required', 'string'],
            'data.*.x' => ['required', 'int', 'min:0'],
            'data.*.y' => ['required', 'int', 'min:0'],
        ]);

        $pdfPath = storage_path("app/public/{$request->filename}");
        $outputPath = storage_path("app/public/data_{$request->filename}");

        if (! file_exists($pdfPath)) {
            return back()->with('error', 'PDF file not found.');
        }

        $pdf = new Fpdi;
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetFont('Helvetica', '', 10);

        $pageCount = $pdf->setSourceFile($pdfPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplIdx);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

            // if ($i == 1) {
            $this->addDynamicData($pdf, $request->data);
            // }
        }

        $pdf->Output($outputPath, 'F');

        return response()->file($outputPath);
    }

    private function addDynamicData(Fpdi $pdf, $data): void
    {
        $pdf->SetTextColor(0, 0, 0);
        foreach ($data as $item) {
            $pdf->SetXY($item['x'], $item['y']);
            $pdf->Cell(0, 10, $item['value'], 0, 1);
        }
    }
}
