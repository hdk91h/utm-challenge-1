<?php

namespace App\Interfaces;

interface ReportGeneratorInterface
{
    public function generate(array $data, string $outputFile): void;
}
