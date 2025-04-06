<?php

namespace App\Services;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfTemplatePrinterService
{
    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws FilterException
     * @throws PdfTypeException
     */
    public function generate(string $pdfPath, string $outputPath, array $data, array $variables): void
    {
        $filteredData = array_filter($data, fn($item) => isset($variables[$item['value']]));
        $filteredData = array_values($filteredData);

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

            $this->addDynamicData($pdf, $filteredData, $variables);
        }

        $pdf->Output($outputPath, 'F');
    }

    private function addDynamicData(Fpdi $pdf, array $data, array $variables): void
    {
        $pdf->SetTextColor(0, 0, 0);

        foreach ($data as $item) {
            $x = $item['x'];
            $y = $item['y'];
            $value = $variables[$item['value']] ?? '';

            $pdf->SetXY($x, $y);

            if (is_array($value)) {
                foreach ($value as $lineIndex => $line) {
                    $pdf->SetXY($x, $y + ($lineIndex * 5)); // 5 is line height spacing
                    $pdf->Cell(0, 10, (string) $line, 0, 1);
                }
            } else {
                $pdf->Cell(0, 10, (string) $value, 0, 1);
            }
        }
    }
}
