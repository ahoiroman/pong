<?php

namespace Ahoiroman\Pong\Exceptions;

use Exception;

class TimerNotStartedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Timer not started.');
    }
}
