<?php

namespace Ahoiroman\Pong;

use Ahoiroman\Pong\Exceptions\MaxValueException;
use Ahoiroman\Pong\Exceptions\NegativeValueException;
use Ahoiroman\Pong\Exceptions\UnknownOSException;
use Ahoiroman\Pong\Interfaces\PingCommand;

class PingCommandBuilder implements PingCommand
{
    protected int     $count;
    protected ?string $host;
    protected float   $interval;
    protected int     $packet_size;
    protected int     $ttl;
    protected int     $timeout;
    protected int     $version;

    public function __construct(?string $host = null)
    {
        if ($this->isValidIPAddress($host)) {
            $this->host = $host;
            $this->setIPAddressVersion();
        } else {
            if (str_ends_with($host, '/')) {
                $host = substr($host, 0, -1);
            }

            // We assume that is an URL...
            //TODO: Needs URL validation
            $pattern = '/^http:\/\/|^https:\/\//';

            if (preg_match($pattern, $host)) {
                $this->host = preg_replace($pattern, '', $host);
            } else {
                $this->host = $host;
            }
        }

        $this->count       = config('ping.count');
        $this->interval    = config('ping.interval');
        $this->packet_size = config('ping.packet_size');
        $this->timeout     = config('ping.timeout');
        $this->ttl         = config('ping.ttl');
    }

    private function isValidIPAddress(?string $ip_address = null): bool
    {
        return IPAddress::Validate($ip_address);
    }

    private function setIPAddressVersion(): void
    {
        if (strpos($this->host, IPAddress::IPV4_SEPARATOR) > 0) {
            $this->useIPv4();
        } elseif (strpos($this->host, IPAddress::IPV6_SEPARATOR) > 0) {
            $this->useIPv6();
        }
    }

    private function useIPv4(): void
    {
        $this->version = 4;
    }

    private function useIPv6(): void
    {
        $this->version = 6;
    }

    public function count(int $count): PingCommandBuilder
    {
        if ($count < 0) {
            throw new NegativeValueException();
        }

        $this->count = $count;

        return $this;
    }

    public function interval(float $interval): PingCommandBuilder
    {
        $this->interval = $interval;

        return $this;
    }

    public function packetSize(int $packet_size): PingCommandBuilder
    {
        $this->packet_size = $packet_size;

        return $this;
    }

    public function ttl(int $ttl): PingCommandBuilder
    {
        if ($ttl > 255) {
            throw new MaxValueException();
        }

        $this->ttl = $ttl;

        return $this;
    }

    public function timeout(int $timeout): PingCommandBuilder
    {
        $this->timeout = $timeout;

        return $this;
    }

    private function getLinuxCommand(): string
    {
        $command = ['ping -n'];

        (!isset($this->version)) ?: array_push($command, '-'.$this->version);
        (!isset($this->count)) ?: array_push($command, '-c '.$this->count);
        (!isset($this->interval)) ?: array_push($command, '-i '.$this->interval);
        (!isset($this->packet_size)) ?: array_push($command, '-s '.$this->packet_size);
        (!isset($this->timeout)) ?: array_push($command, '-W '.$this->timeout);
        (!isset($this->ttl)) ?: array_push($command, '-t '.$this->ttl);

        $command[] = $this->host;

        return implode(' ', $command);
    }

    private function getMacOSCommand(): string
    {
        $command = $this->version === 4 ? ['ping'] : ['ping6'];

        (!isset($this->count)) ?: array_push($command, '-c '.$this->count);
        (!isset($this->interval)) ?: array_push($command, '-i '.$this->interval);
        (!isset($this->packet_size)) ?: array_push($command, '-s '.$this->packet_size);
        (!isset($this->timeout)) ?: array_push($command, '-t '.$this->timeout);
        (!isset($this->ttl)) ?: array_push($command, '-m '.$this->ttl);

        $command[] = $this->host;

        return implode(' ', $command);
    }

    private function getWindowsCommand(): string
    {
        $command = ['ping'];

        (!isset($this->version)) ?: array_push($command, '-'.$this->version);
        (!isset($this->count)) ?: array_push($command, '-n '.$this->count);
        (!isset($this->packet_size)) ?: array_push($command, '-l '.$this->packet_size);
        (!isset($this->timeout)) ?: array_push($command, '-w '.($this->timeout * 1000));
        (!isset($this->ttl)) ?: array_push($command, '-i '.$this->ttl);

        $command[] = $this->host;

        return implode(' ', $command);
    }

    public function getOptions(): object
    {
        $options = [
            'host' => $this->host,
        ];

        (!isset($this->count)) ?: $options['count'] = $this->count;
        (!isset($this->interval)) ?: $options['interval'] = $this->interval;
        (!isset($this->packet_size)) ?: $options['packet_size'] = $this->packet_size;
        (!isset($this->timeout)) ?: $options['timeout'] = $this->timeout;
        (!isset($this->ttl)) ?: $options['ttl'] = $this->ttl;
        (!isset($this->version)) ?: $options['version'] = $this->version;

        return (object)$options;
    }

    public function get(): string
    {
        if (System::isLinux()) {
            return $this->getLinuxCommand();
        }

        if (System::isMacOS()) {
            return $this->getMacOSCommand();
        }

        if (System::isWindows()) {
            return $this->getWindowsCommand();
        }

        throw new UnknownOSException();
    }
}
