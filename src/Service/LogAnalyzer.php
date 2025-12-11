<?php

namespace App\Service;

use App\Model\LogEntry;

/**
 * LogAnalyzer Service sammelt aus einzelnen LogEntry-Objekten einfache Statistiken. Wie oft eine Seriennummer auftrit,
 * welche Mac-Adressen mit welcher Seriennummer verbunden ist und welche Seriennummer zu welcher Hardware gehört.
 */
class LogAnalyzer
{
    private array $accessCounts = [];
    private array $serialToMacs = [];
    private array $hardwareClasses = [];


    /**
     * Logeinträge analysieren
     */
    public function analyze(LogEntry $entry): void
    {

        $serial = $entry->serial;

        if ($serial === 'UNKNOWN') {
            return;
        }

        if (!isset($this->accessCounts[$serial])) {
            $this->accessCounts[$serial] = 0;
        }

        $this->accessCounts[$serial]++;

        $mac = $entry->mac;

        if ($mac !== 'UNKNOWN') {
            $this->serialToMacs[$serial][$mac] = true;
        }

        $hwClass = $entry->specs['cpu'] ?? 'UNKNOWN';

        $this->hardwareClasses[$hwClass][$serial] = true;
    }

    /**
     * Zugriffe der Top 10 Seriennummern
     */
    public function getTopAccessors(int $limit = 10): array
    {
        arsort($this->accessCounts);

        return array_slice($this->accessCounts, 0, $limit, true);
    }

    /**
     * Lizenzverletzungen Mac > Seriennummer
     */
    public function getLicenseViolationEntries(int $limit = 10): array
    {
        $violators = [];

        foreach ($this->serialToMacs as $serial => $macs) {
            $deviceCount = count($macs);

            if ($deviceCount > 1) {
                $violators[$serial] = $deviceCount;
            }
        }

        arsort($violators);

        return array_slice($violators, 0, $limit, true);
    }

    /**
     * Hardware Klassen Statistik
     */
    public function getHardwareStats(): array
    {
        $stats = [];

        foreach ($this->hardwareClasses as $type => $serials) {
            $stats[$type] = count($serials);
        }

        arsort($stats);

        return $stats;
    }
}
