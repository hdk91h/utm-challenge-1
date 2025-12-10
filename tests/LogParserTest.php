<?php

use PHPUnit\Framework\TestCase;
use App\Service\LogParser;
use App\Model\LogEntry;

class LogParserTest extends TestCase
{
    public function testParser()
    {

        // {"mac":"AA:BB:CC:DD:EE:FF", "cpu":"intel"} | gzip | base64
        $validSpecs = "H4sIAAAAAAAAA6tWyk1MVrJScnS0cnKycna2cnGxcnW1cnNT0lFQSi4oBUpl5pWk5ijVAgAItC4sKgAAAA==";

        $line = '71.215.209.250 update-server [21/Jul/1991:13:37:00] "GET /update.sh HTTP/1.0" 200 2427 proxy=proxy-002 serial=SERIAL123 specs=' . $validSpecs;

        $parser = new LogParser();

        $entry = $parser->parse($line);

        $this->assertInstanceOf(LogEntry::class, $entry);
        $this->assertEquals('71.215.209.250', $entry->ip);
        $this->assertEquals('SERIAL123', $entry->serial);

        $this->assertEquals('AA:BB:CC:DD:EE:FF', $entry->mac);
        $this->assertArrayHasKey('cpu', $entry->specs);
    }
}
