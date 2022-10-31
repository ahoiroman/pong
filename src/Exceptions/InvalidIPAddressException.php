<?php

namespace Ahoiroman\Pong\Exceptions;

use Exception;

class InvalidIPAddressException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid IP Address');
    }
}
