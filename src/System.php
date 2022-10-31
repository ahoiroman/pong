<?php

namespace Ahoiroman\Pong;

class System
{
    public static function isLinux(): bool
    {
        return PHP_OS_FAMILY === 'Linux';
    }

    public static function isMacOS(): bool
    {
        return PHP_OS_FAMILY === 'Darwin';
    }

    public static function isWindows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
