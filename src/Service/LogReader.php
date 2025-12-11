<?php

namespace App\Service;

/**
 * LogReader Service liest Logdateien zeilenweise ein.
 */
class LogReader
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Datei zeilenweise lesen, da sonst wahrscheinlich Memory Leak
     */
    public function getLines(): \Generator
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException("Log file not found: " . $this->filePath);
        }

        $handle = fopen($this->filePath, 'r');

        if (!$handle) {
            throw new \RuntimeException("Could not open log file: " . $this->filePath);
        }

        try {
            while (($line = fgets($handle)) !== false) {
                yield trim($line);
            }
        } finally {
            fclose($handle);
        }
    }
}
