<?php

namespace GeminiLabs\Vectorface\Whip\IpRange;

/**
 * A class representing an IPv4 address range.
 * @copyright Vectorface, Inc 2015
 * @author Daniel Bruce <dbruce1126@gmail.com>
 */
class Ipv4Range implements IpRange
{
    /** the lower value of the range (as a long integer) */
    private int $lowerInt;

    /** the upper value of the range (as a long integer) */
    private int $upperInt;

    /**
     * Constructor for the class.
     *
     * @param string $range A valid IPv4 range as a string. Supported range styles:
     *        - CIDR notation (127.0.0.1/24)
     *        - hyphen notation (127.0.0.1-127.0.0.255)
     *        - wildcard notation (127.0.0.*)
     *        - a single specific IP address (127.0.0.1)
     */
    public function __construct(string $range)
    {
        $this->computeLowerAndUpperBounds($range);
    }

    /**
     * Returns the lower value of the IPv4 range as a long integer.
     *
     * @return int The lower value of the IPv4 range.
     */
    public function getLowerInt(): int
    {
        return $this->lowerInt;
    }

    /**
     * Returns the upper value of the IPv4 range as a long integer.
     *
     * @return int The upper value of the IPv4 range.
     */
    public function getUpperInt(): int
    {
        return $this->upperInt;
    }

    /**
     * Returns whether a given IP address falls within this range.
     *
     * @param string $ipAddress The given IP address.
     * @return bool Returns true if the IP address falls within the range
     *         and false otherwise.
     */
    public function containsIp(string $ipAddress): bool
    {
        $ipLong = ip2long($ipAddress);
        return ($this->getLowerInt() <= $ipLong) && ($this->getUpperInt() >= $ipLong);
    }

    /**
     * Computes the lower and upper bounds of the IPv4 range by parsing the
     * range string.
     *
     * @param string $range The IPv4 range as a string.
     */
    private function computeLowerAndUpperBounds(string $range): void
    {
        // support CIDR notation
        if (str_contains($range, '/')) {
            [$this->lowerInt, $this->upperInt] = $this->parseCidrRange($range);
            return;
        }

        // support for IP ranges like '10.0.0.0-10.0.0.255'
        if (str_contains($range, '-')) {
            [$this->lowerInt, $this->upperInt] = $this->parseHyphenRange($range);
            return;
        }

        // support for IP ranges like '10.0.*'
        if (str_contains($range, '*')) {
            [$this->lowerInt, $this->upperInt] = $this->parseWildcardRange($range, strpos($range, '*'));
            return;
        }

        // assume we have a single address
        $this->lowerInt = ip2long($range);
        $this->upperInt = $this->lowerInt;
    }

    /**
     * Parses a CIDR notation range.
     *
     * @param string $range The CIDR range.
     * @return array Returns an array with the first element being the lower
     *         bound of the range and second element being the upper bound.
     */
    private function parseCidrRange(string $range): array
    {
        [$address, $mask] = explode('/', $range);
        $mask = (int) $mask;
        $longAddress = ip2long($address);
        return [
            $longAddress & (((1 << $mask) - 1) << (32 - $mask)),
            $longAddress | ((1 << (32 - $mask)) - 1)
        ];
    }

    /**
     * Parses a hyphen notation range.
     *
     * @param string $range The hyphen notation range.
     * @return array Returns an array with the first element being the lower
     *         bound of the range and second element being the upper bound.
     */
    private function parseHyphenRange(string $range) : array
    {
        return array_map('ip2long', explode('-', $range));
    }

    /**
     * Parses a wildcard notation range.
     *
     * @param string $range The wildcard notation range.
     * @param int $pos The integer position of the wildcard within the range string.
     * @return array Returns an array with the first element being the lower
     *         bound of the range and second element being the upper bound.
     */
    private function parseWildcardRange(string $range, int $pos) : array
    {
        $prefix = substr($range, 0, $pos - 1);
        $parts  = explode('.', $prefix);
        $partsCount = 4 - count($parts);
        return [
            ip2long(implode('.', array_merge($parts, array_fill(0, $partsCount, 0)))),
            ip2long(implode('.', array_merge($parts, array_fill(0, $partsCount, 255))))
        ];
    }
}
