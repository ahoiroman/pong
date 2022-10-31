<?php

namespace Ahoiroman\Pong\Parsers;

final class PingParserForWindows extends PingParser
{
    private bool $unreachable;

    public function __construct(array $ping)
    {
        parent::__construct($ping);

        $this->unreachable = $this->isUnreachable($ping);

        if ($this->unreachable) {
            $this->setStatistics($ping[count($ping) - 2]);
        } else {
            $this->setRoundTripTime($ping[count($ping) - 1]);
            $this->setSequence();
            $this->setStatistics($ping[count($ping) - 4]);
        }

        $this->setHostStatus();
    }

    private function getHostStatus(): string
    {
        return ($this->statistics['packet_loss'] < 100) ? 'Ok' : 'Unreachable';
    }

    private function getSequence(): array
    {
        $ping = $this->results;

        $items_count = count($ping);

        // First remove items from final of the array
        for ($i = 6; $i > 0; $i--) {
            unset($ping[$items_count - $i]);
        }

        // Then remove first items
        unset($ping[1]);
        unset($ping[0]);

        $key = 0;

        $sequence = [];

        foreach ($ping as $row) {
            $sequence[$key] = $row;

            $key++;
        }

        return $sequence;
    }

    private function isUnreachable(array $ping): bool
    {
        $needles = 'perdidos|lost';

        $result = $ping[count($ping) - 1];

        $unreachable = false;

        foreach (explode('|', $needles) as $needle) {
            $search = strpos($result, '100% '.$needle);

            if ($search !== false) {
                $unreachable = true;
                break;
            }
        }

        return $unreachable;
    }

    private function parseRoundTripTime(string $row): array
    {
        $rtt = explode(',', str_replace('ms', '', $row));

        $min = (float) explode(' = ', $rtt[0])[1] / 1000;
        $max = (float) explode(' = ', $rtt[1])[1] / 1000;
        $avg = (float) explode(' = ', $rtt[2])[1] / 1000;

        return [
            'avg' => $avg,
            'min' => $min,
            'max' => $max,
        ];
    }

    private function parseStatistics(string $row): array
    {
        $ping_statistics = explode(', ', explode(':', $row)[1]);

        $transmitted = (int) explode(' = ', $ping_statistics[0])[1];

        $received = (int) explode(' = ', $ping_statistics[1])[1];

        $lost = (int) explode(' = ', $ping_statistics[2])[1];

        return [
            'packets_transmitted' => $transmitted,
            'packets_received'    => $received,
            'packets_lost'        => $lost,
            'packet_loss'         => (int) (100 - (($received * 100) / $transmitted)),
        ];
    }

    private function setHostStatus(): void
    {
        $this->host_status = $this->getHostStatus();
    }

    private function setRoundTripTime(string $rtt): void
    {
        $this->round_trip_time = $this->parseRoundTripTime($rtt);
    }

    private function setSequence(): void
    {
        $this->sequence = $this->getSequence();
    }

    private function setStatistics(string $statistics): void
    {
        $this->statistics = $this->parseStatistics($statistics);
    }

    public function parse(): object
    {
        $parsed = [
            'host_status' => $this->host_status,
            'raw'         => $this->results,
        ];

        if (count($this->round_trip_time) > 0) {
            $parsed['latency'] = $this->round_trip_time['avg'];
            $parsed['rtt'] = (object) $this->round_trip_time;
        }

        if (count($this->sequence) > 0) {
            $parsed['sequence'] = (object) $this->sequence;
        }

        if (count($this->statistics) > 0) {
            $parsed['statistics'] = (object) $this->statistics;
        }

        return (object) $parsed;
    }
}
