<?php

use PHPUnit\Framework\TestCase;
use App\Service\LogAnalyzer;
use App\Model\LogEntry;

class LogAnalyzerTest extends TestCase
{
    private LogAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new LogAnalyzer();
    }

    public function testAnalyzer()
    {

        $entries = [
            // Poweruser
            $this->createEntry('SERIAL_A', 'MAC_1', 'Intel'),
            $this->createEntry('SERIAL_A', 'MAC_1', 'Intel'),
            $this->createEntry('SERIAL_A', 'MAC_1', 'Intel'),

            // Violator
            $this->createEntry('SERIAL_B', 'MAC_2', 'AMD'),
            $this->createEntry('SERIAL_B', 'MAC_3', 'AMD'),

            // Normaluser
            $this->createEntry('SERIAL_C', 'MAC_4', 'Intel'),

            // Unknown
            $this->createEntry('UNKNOWN', 'MAC_5', 'ARM'),
        ];

        foreach ($entries as $entry) {
            $this->analyzer->analyze($entry);
        }

        // Die meisten Reqs
        $accessors = $this->analyzer->getTopAccessors();

        $this->assertArrayHasKey('SERIAL_A', $accessors);
        $this->assertEquals(3, $accessors['SERIAL_A'], 'SERIAL_A sollte 3 reqs haben.');

        $this->assertArrayHasKey('SERIAL_B', $accessors);
        $this->assertEquals(2, $accessors['SERIAL_B'], 'SERIAL_B sollte 2 reqs haben.');

        $this->assertArrayNotHasKey('UNKNOWN', $accessors, 'UNKNOWN sollte nicht in Top Accessors sein.');

        // Violator
        $violators = $this->analyzer->getLicenseViolationEntries();

        $this->assertArrayHasKey('SERIAL_B', $violators);
        $this->assertEquals(2, $violators['SERIAL_B'], 'SERIAL_B sollte 2 verschiedene MACs haben.');

        $this->assertArrayNotHasKey('SERIAL_A', $violators, 'SERIAL_A sollte kein Violator sein.');

        // HW Stats

        $hwStats = $this->analyzer->getHardwareStats();

        $this->assertArrayHasKey('Intel', $hwStats);
        $this->assertEquals(2, $hwStats['Intel'], 'Es sollte 2 Seriennummern mit Intel CPU geben.');

        $this->assertArrayHasKey('AMD', $hwStats);
        $this->assertEquals(1, $hwStats['AMD'], 'Es sollte 1 Seriennummer mit AMD CPU geben.');
    }

    public function createEntry($serial, $mac, $cpu): LogEntry
    {
        return new LogEntry(
            '127.0.0.1',
            $serial,
            $mac,
            ['cpu' => $cpu]
        );
    }
}
