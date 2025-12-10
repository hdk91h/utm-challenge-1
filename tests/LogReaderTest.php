<?php

use PHPUnit\Framework\TestCase;
use App\Service\LogReader;

class LogReaderTest extends TestCase
{
    public function testFileAndLinesRead()
    {

        $tempFile = sys_get_temp_dir() . 'test_log.txt';
        file_put_contents($tempFile, "Eins\nZwei\nDrei");

        $reader = new LogReader($tempFile);

        $lines = [];

        foreach ($reader->getLines() as $line) {
            $lines[] = $line;
        }

        $this->assertCount(3, $lines);
        $this->assertEquals("Eins", $lines[0]);
        $this->assertEquals("Zwei", $lines[1]);
        $this->assertEquals("Drei", $lines[2]);

        unlink($tempFile);
    }
}
