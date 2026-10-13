<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\Vectorface\Whip\Whip;

/**
 * The IP address of the visitor who sent the request.
 */
class ClientIp
{
    public function address(): string
    {
        $proxyHeader = $this->proxyHeader();
        $whitelist = glsr()->filterArray('whip/whitelist', $this->whitelist($proxyHeader));
        $whip = new Whip(Whip::REMOTE_ADDR | Whip::CUSTOM_HEADERS, $whitelist);
        if (!empty($proxyHeader)) {
            $whip->addCustomHeader($proxyHeader);
        }
        glsr()->action('whip', $whip);
        if (false !== ($clientAddress = $whip->getValidIpAddress())) {
            return (string) $clientAddress;
        }
        glsr_log()->error('Unable to detect IP address, please see the FAQ page for a possible solution.');
        return 'unknown';
    }

    /**
     * @return string[] the lines of a Trusted Proxies list that are not blank
     */
    public function lines(string $text): array
    {
        $lines = array_map('trim', (array) preg_split('/\R/', $text));
        return array_values(array_filter($lines, 'strlen'));
    }

    /**
     * @return string[] the valid lines of a Trusted Proxies list
     */
    public function ranges(string $text): array
    {
        return array_values(array_filter($this->lines($text), [$this, 'isRange']));
    }

    /**
     * Whip casts the mask to an integer and does not check it. A mask that is
     * not a number matches every address. An IPv4 mask above 32 throws.
     */
    protected function isRange(string $range): bool
    {
        [$ip, $mask] = array_pad(explode('/', $range, 2), 2, null);
        if (false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            $bits = 32;
        } elseif (false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
            $bits = 128;
        } else {
            return false;
        }
        if (is_null($mask)) {
            return true;
        }
        return ctype_digit($mask) && (int) $mask <= $bits;
    }

    protected function proxyHeader(): string
    {
        return $this->setting()->sanitize('proxy_http_header', 'id');
    }

    protected function setting(): Arguments
    {
        return glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
    }

    protected function whitelist(string $proxyHeader): array
    {
        if (empty($proxyHeader)) {
            return [];
        }
        $ranges = $this->ranges($this->setting()->sanitize('trusted_proxies', 'text-multiline'));
        $isIpv4 = function (string $range): bool {
            [$ip] = explode('/', $range);
            return false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
        };
        $ipv4 = array_values(array_filter($ranges, $isIpv4));
        $ipv6 = array_values(array_diff($ranges, $ipv4));
        return [
            $proxyHeader => [
                Whip::IPV4 => $ipv4,
                Whip::IPV6 => $ipv6,
            ],
        ];
    }
}
