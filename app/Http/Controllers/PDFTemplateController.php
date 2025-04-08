<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Services\PDFGridService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

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

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
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

    /**
     * @throws \Throwable
     */
    public function destroy($id)
    {
        $template = PdfTemplate::query()->findOrFail($id);

        DB::transaction(function () use ($template) {
            Storage::disk('public')->delete($template->pdf_path);
            Storage::disk('public')->delete($template->grid_pdf_path);
            $template->delete();
        });

        return redirect()->route('pdf-templates.index')->with('success', 'PDF deleted successfully.');
    }
}
