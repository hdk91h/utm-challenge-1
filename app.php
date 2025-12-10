<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\LogRunner;
use App\Service\LogParser;
use App\Service\LogReader;
use App\Service\LogAnalyzer;
use App\Service\PdfReportGenerator;

$data = __DIR__ . '/data/access.log';
$output = __DIR__ . '/output/report.pdf';

$runner = new LogRunner(
    new LogParser(),
    new LogAnalyzer(),
    new LogReader($data),
    new PdfReportGenerator()
);
$runner->run($data, $output);
