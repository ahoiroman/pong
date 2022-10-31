<?php

namespace Ahoiroman\Pong;

class IPAddress
{
    public const IPV4_SEPARATOR = '.';
    public const IPV6_SEPARATOR = ':';

    public static function Validate(string $ip_address): bool
    {
        if (str_contains($ip_address, IPAddress::IPV4_SEPARATOR)) {
            return self::validateIPv4Address($ip_address);
        }

        if (str_contains($ip_address, IPAddress::IPV6_SEPARATOR)) {
            return self::validateIPv6Address($ip_address);
        }

        return false;
    }

    private static function validateIPv4Address(string $ip_address): bool
    {
        if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }

        return true;
    }

    private static function validateIPv6Address(string $ip_address): bool
    {
        if (filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            return false;
        }

        return true;
    }
}
