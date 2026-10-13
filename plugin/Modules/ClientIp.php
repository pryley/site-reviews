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
     * @return string[] the valid lines of a Trusted Proxies list
     */
    public function ranges(string $text): array
    {
        $lines = array_map('trim', (array) preg_split('/\R/', $text));
        return array_values(array_filter($lines, [$this, 'isRange']));
    }

    protected function isRange(string $range): bool
    {
        [$ip] = explode('/', $range);
        return false !== filter_var($ip, \FILTER_VALIDATE_IP);
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
