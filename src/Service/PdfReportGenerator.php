<?php

namespace App\Service;

use TCPDF;

/**
 * PdfReportGenerator Service erstellt PDF-Berichte aus den analysierten Logdaten.
 */
class PdfReportGenerator
{
    public function generate(array $data, string $outputPath): void
    {

        $pdf = new TCPDF(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        $pdf->setCreator(('UTM Challenge App'));
        $pdf->setAuthor('Hendrik');
        $pdf->setTitle('UTM Log Report');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->setMargins(15, 15, 15);
        $pdf->setAutoPageBreak(TRUE, 15);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'UTM Log Report', 0, 1, 'C');
        $pdf->Ln(10);

        $this->printSectionTitle($pdf, '1. Top 10 Accessors');
        $this->printTable($pdf, ['Serial Number', 'Request Count'], $data['topAccessors']);

        $pdf->Ln(10);

        $this->printSectionTitle($pdf, '2. License Violations');
        $this->printTable($pdf, ['Serial Number', 'Unique MAC Count'], $data['licenseViolations']);

        $pdf->AddPage();

        $this->printSectionTitle($pdf, '3. Hardware Statistics');
        $this->printHardwareTable($pdf, $data['hardware']);

        $pdf->Output($outputPath, 'F');
    }

    private function printSectionTitle($pdf, $title): void
    {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $title, 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);
    }

    private function printTable($pdf, array $header, array $data): void
    {
        $w = [140, 40];

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell($w[0], 7, $header[0], 1, 0, 'L');
        $pdf->Cell($w[1], 7, $header[1], 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 12);

        if (empty($data)) {
            $pdf->Cell(array_sum($w), 7, 'No entries found.', 1, 1, 'C');
            return;
        }

        foreach ($data as $key => $value) {
            $pdf->Cell($w[0], 6, $key, 1, 0, 'L');
            $pdf->Cell($w[1], 6, (string)$value, 1, 0, 'R');
            $pdf->Ln();
        }
    }

    private function printHardwareTable($pdf, array $data): void
    {
        $w = [140, 40];

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell($w[0], 7, 'Hardware Class / CPU', 1, 0, 'L');
        $pdf->Cell($w[1], 7, 'Active Licenses', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 9);

        foreach ($data as $hwName => $count) {
            $cleanName = rtrim($hwName, '/');
            $pdf->Cell($w[0], 6, substr($cleanName, 0, 80), 1, 0, 'L');
            $pdf->Cell($w[1], 6, $count, 1, 0, 'R');
            $pdf->Ln();
        }
    }
}
