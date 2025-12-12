<?php

namespace App\Interfaces;

/**
 * ReportGeneratorInterface entkoppelt die Logik, so lassen sich verschiedene Generatoren implementieren.
 */
interface ReportGeneratorInterface
{
    public function generate(array $data, string $outputFile): void;
}
