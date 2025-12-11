<?php

namespace App\Service;

use App\Model\LogEntry;

/**
 * LogParser Service parsed die Logzeilen und erstellt LogEntry-Objekte. Außerdem werden Metadaten extrahiert und Specs decodiert.
 */
class LogParser
{

    // https://hamatti.org/posts/parsing-nginx-server-logs-with-regular-expressions/
    private const LINE_REGEX = '/^(?P<ip>\S+) (?P<server>\S+) \[(?P<date>[^\]]+)\] "(?P<request>[^"]+)" (?P<status>\d+) (?P<size>\d+) (?P<metadata>.*)$/';

    public function parse(string $line): ?LogEntry
    {

        if (!preg_match(self::LINE_REGEX, $line, $matches)) {
            return null;
        }

        $metaData = $this->parseMetadata($matches['metadata']);

        $specs = [];

        if (isset($metaData['specs'])) {
            $specs = $this->decodeSpecs($metaData['specs']);
        }

        return new LogEntry(
            $matches['ip'],
            $metaData['serial'] ?? 'UNKNOWN',
            $specs['mac'] ?? 'UNKNOWN',
            $specs
        );
    }

    public function parseMetadata(string $metaString): array
    {
        $metaData = [];

        // "value" oder value finden
        preg_match_all('/(\w+)=(?:"([^"]*)"|(\S+))/', $metaString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = !empty($match[3]) ? $match[3] : $match[2];
            $metaData[$key] = $value;
        }

        return $metaData;
    }

    public function decodeSpecs(string $base64): array
    {
        try {

            $binary = base64_decode($base64);

            if (!$binary) {
                return [];
            }

            $json = @gzdecode($binary);

            if (!$json) {
                return [];
            }

            return json_decode($json, true) ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
