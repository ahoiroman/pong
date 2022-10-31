<?php

namespace Ahoiroman\Pong;

use Ahoiroman\Pong\Exceptions\TimerNotStartedException;
use DateTime;

class Timer
{
    /**
     * Format for the timestamps.
     */
    public const FORMAT = 'd-m-Y H:i:s.u';

    protected float $start;

    protected float $stop;

    public function __construct(protected string $format = self::FORMAT)
    {
        return $this;
    }

    public function start(): float
    {
        return $this->start = microtime(true);
    }

    public function stop(): float
    {
        if (!isset($this->start)) {
            throw new TimerNotStartedException();
        }

        return $this->stop = microtime(true);
    }

    public function getResults(): object
    {
        if (!isset($this->stop)) {
            $this->stop = microtime(true);
        }

        return (object) [
            'start' => $this->getTimeObject($this->start),
            'stop'  => $this->getTimeObject($this->stop),
            'time'  => $this->getTimeElapsed(),
        ];
    }

    private static function getDateTimeObjectFromTimeStamp(float $timestamp): DateTime
    {
        return DateTime::createFromFormat('U.u', $timestamp);
    }

    private function getTimeObject(float $timestamp): object
    {
        $date_time = self::getDateTimeObjectFromTimeStamp($timestamp);

        return (object) [
            'as_float'       => $timestamp,
            'human_readable' => $date_time->format($this->format),
        ];
    }

    private function getTimeElapsed(): float
    {
        $time_elapsed = $this->stop - $this->start;

        return round($time_elapsed, 3, PHP_ROUND_HALF_DOWN);
    }
}
