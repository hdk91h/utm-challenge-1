<?php

namespace App\Service;

use App\Service\LogParser;
use App\Service\LogAnalyzer;
use App\Service\PdfReportGenerator;

/**
 * LogRunner Service koordiniert das Einlesen, Parsen, Analysieren der Logs und die Berichtserstellung.
 */
class LogRunner
{
    private LogParser $parser;
    private LogAnalyzer $analyzer;
    private LogReader $reader;
    private PdfReportGenerator $pdfGenerator;

    public function __construct(
        LogParser $parser,
        LogAnalyzer $analyzer,
        LogReader $reader,
        PdfReportGenerator $pdfGenerator
    ) {
        $this->parser = $parser;
        $this->analyzer = $analyzer;
        $this->reader = $reader;
        $this->pdfGenerator = $pdfGenerator;
    }

    public function run(string $inputFile, string $outputPdf): void
    {

        echo 'Read data from ' . $inputFile . "\n";

        $lineCount = 0;
        $validCount = 0;

        foreach ($this->reader->getLines($inputFile) as $line) {
            $lineCount++;
            $entry = $this->parser->parse(trim($line));

            if ($entry !== null) {
                $this->analyzer->analyze($entry);
                $validCount++;
            }
        }

        echo "Parsed $lineCount lines, got $validCount valid entries.\n";

        $topAccessors = $this->analyzer->getTopAccessors(10);
        $licenseViolations = $this->analyzer->getLicenseViolationEntries(10);
        $hardwareStats = $this->analyzer->getHardwareStats();

        $this->printReport($topAccessors, $licenseViolations, $hardwareStats);

        echo "\n Generating PDF report to " . $outputPdf . "...\n";

        $reportData = [
            'topAccessors' => $topAccessors,
            'licenseViolations' => $licenseViolations,
            'hardware' => $hardwareStats
        ];

        $this->pdfGenerator->generate($reportData, $outputPdf);
    }

    public function printReport(array $accessors, array $violaators, array $hardwareStats): void
    {

        echo "\n Aufgabe 1: Top 10 Accessors \n";
        foreach ($accessors as $serial => $count) {
            echo "Seriennummer: $serial - Zugriffe: $count \n";
        }

        echo "\n Aufgabe 2: Lizenzverletzungen \n";
        foreach ($violaators as $serial => $macCount) {
            echo "Seriennummer: $serial - Verschiedene MACs: $macCount \n";
        }

        echo "\n Aufgabe 3: Hardware Statistik \n";
        foreach ($hardwareStats as $type => $count) {
            echo "Hardware Klasse: $type - Einzigartige Seriennummern: $count \n";
        }
    }
}
