<?php

namespace Ahoiroman\Pong\Exceptions;

use Exception;

class MaxValueException extends Exception
{
    public function __construct()
    {
        parent::__construct('TTL must not exceed 255 value.');
    }
}
