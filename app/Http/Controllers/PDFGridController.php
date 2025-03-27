<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Services\PDFGridService;
use Illuminate\Http\Request;

class PDFGridController extends Controller
{
    public function __construct(protected PDFGridService $pdfGridService) {}

    public function uploadPDF(Request $request)
    {
        dd(123);
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:4096',
        ]);

        // Store original PDF
        $pdfPath = $request->file('pdf')->store('pdf_templates', 'public');

        // Add Grid to the PDF
        $gridPdfPath = $this->pdfGridService->addGridToPDF($pdfPath);

        // Save template with grid version
        $template = PdfTemplate::query()->create([
            'pdf_path' => $gridPdfPath,
        ]);

        return redirect()->route('grid', ['templateId' => $template->id]);
    }

    public function showGrid($templateId)
    {
        $template = PdfTemplate::with('fields')->findOrFail($templateId);

        return view('grid', ['template' => $template]);
    }
}
