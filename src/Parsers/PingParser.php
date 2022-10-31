<?php

namespace Ahoiroman\Pong\Parsers;

use Ahoiroman\Pong\Interfaces\PingParserInterface;

class PingParser implements PingParserInterface
{
    protected string $host_status = '';
    protected array $sequence = [];
    protected array $statistics = [];
    protected array $round_trip_time = [];

    public function __construct(protected array $results = [])
    {
    }

    public function parse(): object
    {
        return (object) $this->results;
    }
}
