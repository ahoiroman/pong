<?php

namespace Ahoiroman\Pong\Exceptions;

use Exception;

class PingFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Ping execution failed.');
    }
}
