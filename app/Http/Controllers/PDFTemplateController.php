<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Services\PDFGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PDFTemplateController extends Controller
{
    public function __construct(public PDFGridService $pdfGridService) {}

    public function index()
    {
        $pdfTemplates = PdfTemplate::all();

        return view('pdf_templates.index', ['pdfTemplates' => $pdfTemplates]);
    }

    public function create()
    {
        return view('pdf_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:4096',
        ]);

        // Store original PDF
        $pdfPath = $request->file('pdf')->store('pdf_templates', 'public');

        $originalFilename = $request->file('pdf')->getClientOriginalName();

        // Add Grid to the PDF
        $gridPdfPath = $this->pdfGridService->addGridToPDF($pdfPath);

        // Save template with grid version
        PdfTemplate::query()->create([
            'name' => $originalFilename,
            'pdf_path' => $pdfPath,
            'grid_pdf_path' => $gridPdfPath,
        ]);

        return redirect()->route('pdf-templates.index')->with('success',
            'PDF uploaded successfully with grid overlay.');
    }

    public function show($id, Request $request)
    {
        $grid = $request->query('grid', false); // Default to false if not provided

        $template = PdfTemplate::query()->findOrFail($id);

        return view('pdf_templates.show', ['template' => $template, 'grid' => $grid]);
    }

    // Delete a PDF template
    public function destroy($id)
    {
        $template = PdfTemplate::query()->findOrFail($id);
        Storage::delete('public/'.$template->pdf_path);
        $template->delete();

        return redirect()->route('pdf-templates.index')->with('success', 'PDF deleted successfully.');
    }
}
