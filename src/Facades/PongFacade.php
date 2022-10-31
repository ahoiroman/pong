<?php

namespace Ahoiroman\Pong\Facades;

use Illuminate\Support\Facades\Facade;

class PongFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'pong';
    }
}
