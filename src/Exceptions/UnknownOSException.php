<?php

namespace Ahoiroman\Pong\Exceptions;

use Exception;

class UnknownOSException extends Exception
{
    public function __construct()
    {
        parent::__construct('Unknown OS');
    }
}
