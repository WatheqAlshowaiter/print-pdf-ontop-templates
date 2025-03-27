<?php

namespace App\Services;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;

class PDFGridService
{
    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    public function addGridToPDF($pdfPath): string
    {
        $fullPath = storage_path("app/public/{$pdfPath}");
        $outputPath = storage_path("app/public/grid_{$pdfPath}");

        $pdf = new Fpdi;
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetFont('Helvetica', '', 5); // Even smaller font for better fit

        // Import the original PDF
        $pageCount = $pdf->setSourceFile($fullPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplIdx);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

            // Overlay the grid with coordinates
            $this->drawGrid($pdf, $size['width'], $size['height']);
        }

        $pdf->Output($outputPath, 'F');

        return "grid_{$pdfPath}";
    }

    private function drawGrid(Fpdi $pdf, $width, $height, $cellSize = 10): void // Reduced cell size
    {
        $pdf->SetDrawColor(220, 220, 220); // Light gray for grid lines
        $pdf->SetLineWidth(0.1);
        $pdf->SetTextColor(160, 160, 160); // Slightly lighter text to blend better

        for ($x = 0; $x <= $width; $x += $cellSize) {
            $pdf->Line($x, 0, $x, $height);
        }

        for ($y = 0; $y <= $height; $y += $cellSize) {
            $pdf->Line(0, $y, $width, $y);
        }

        // Add (x, y) coordinates in each cell
        for ($x = 0; $x < $width; $x += $cellSize) {
            for ($y = 0; $y < $height; $y += $cellSize) {
                $pdf->SetXY($x + 1, $y + 1); // Adjusted for smaller cell
                $pdf->Cell(10, 3, "({$x},{$y})", 0, 0, '', false);
            }
        }
    }
}
