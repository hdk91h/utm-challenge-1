<?php

namespace App\Model;

class LogEntry
{
    public string $ip;
    public string $serial;
    public string $mac;
    public array $specs;

    public function __construct(string $ip, string $serial, string $mac, array $specs)
    {
        $this->ip = $ip;
        $this->serial = $serial;
        $this->mac = $mac;
        $this->specs = $specs;
    }
}
