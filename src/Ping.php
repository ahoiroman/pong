<?php

namespace Ahoiroman\Pong;

use Ahoiroman\Pong\Exceptions\PingFailedException;
use Ahoiroman\Pong\Exceptions\UnknownOSException;
use Ahoiroman\Pong\Parsers\PingParserForLinux;
use Ahoiroman\Pong\Parsers\PingParserForMacOS;
use Ahoiroman\Pong\Parsers\PingParserForWindows;
use Exception;

class Ping
{
    protected PingCommandBuilder $command;

    protected Timer $timer;

    public function __construct(PingCommandBuilder $command)
    {
        $this->command = $command;

        $this->timer = new Timer();
        $this->timer->start();
    }

    protected function parse(array $ping): object
    {
        if (System::isLinux()) {
            return (new PingParserForLinux($ping))->parse();
        }

        if (System::isMacOS()) {
            return (new PingParserForMacOS($ping))->parse();
        }

        if (System::isWindows()) {
            return (new PingParserForWindows($ping))->parse();
        }

        throw new UnknownOSException();
    }

    private function cleanBinaryString(array $ping): array
    {
        $cleaned = [];

        foreach ($ping as $row) {
            $cleaned[] = preg_replace('/[[:^print:]]/', '', $row);
        }

        return $cleaned;
    }

    /**
     * @throws Exception
     */
    public function run(): object
    {
        $ping = $this->executePing();

        // Return the result if lines count are less than three.
        if (count($ping) < 3) {
            return (object) $ping;
        }

        $ping_object = ($this->parse($ping));

        $ping_object->options = $this->command->getOptions();
        $ping_object->time = $this->timer->getResults();

        return $ping_object;
    }

    private function executePing(): array
    {
        exec($this->command->get(), $exec_result);

        if (!is_array($exec_result)) {
            throw new PingFailedException();
        }

        return $this->cleanBinaryString($exec_result);
    }
}
