<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\Vectorface\Whip\Whip;

/**
 * The IP address of the visitor who sent the request.
 */
class ClientIp
{
    public function address(): string
    {
        $whip = new Whip(Whip::REMOTE_ADDR | Whip::CUSTOM_HEADERS, [
            Whip::CUSTOM_HEADERS => $this->trustedProxies(),
        ]);
        $proxyHeader = $this->proxyHeader();
        if (!empty($proxyHeader)) {
            $whip->addCustomHeader($proxyHeader);
        }
        glsr()->action('whip', $whip);
        // Whip throws when it checks a missing address against the trusted proxies.
        if ($this->hasPeer() && false !== ($clientAddress = $whip->getValidIpAddress())) {
            return (string) $clientAddress;
        }
        glsr_log()->error('Unable to detect IP address, please see the FAQ page for a possible solution.');
        return 'unknown';
    }

    /**
     * @return array<string, string> the proxy headers the tool lists, as name => label
     */
    public function headers(): array
    {
        return [
            'cf-connecting-ip' => 'CF-Connecting-IP',
            'client-ip' => 'Client-IP',
            'forwarded' => 'Forwarded',
            'forwarded-for' => 'Forwarded-For',
            'incap-client-ip' => 'Incap-Client-IP',
            'x-cluster-client-ip' => 'X-Cluster-Client-IP',
            'x-forwarded' => 'X-Forwarded',
            'x-forwarded-for' => 'X-Forwarded-For',
            'x-real-ip' => 'X-Real-IP',
        ];
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
     * The name in the form the tool saves it: "HTTP_X_REAL_IP" is "x-real-ip".
     */
    public function normalizeHeader(string $header): string
    {
        $header = Str::removePrefix(strtolower(trim($header)), 'http_');
        return glsr(Sanitizer::class)->sanitizeId(str_replace('_', '-', $header));
    }

    public function proxyHeader(): string
    {
        return $this->setting()->sanitize('proxy_http_header', 'id');
    }

    /**
     * @return string[] the valid lines of a Trusted Proxies list
     */
    public function ranges(string $text): array
    {
        return array_values(array_filter($this->lines($text), [$this, 'isRange']));
    }

    /**
     * @param string[] $ranges
     *
     * @return array{ipv4: string[], ipv6: string[]}
     */
    protected function groupRanges(array $ranges): array
    {
        $ipv4 = array_filter($ranges, function (string $range): bool {
            [$ip] = explode('/', $range);
            return false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
        });
        return [
            Whip::IPV4 => array_values($ipv4),
            Whip::IPV6 => array_values(array_diff($ranges, $ipv4)),
        ];
    }

    /**
     * The peer is the address that connected to the web server. A command line
     * request has none.
     */
    protected function hasPeer(): bool
    {
        return false !== filter_var($_SERVER['REMOTE_ADDR'] ?? '', \FILTER_VALIDATE_IP);
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

    protected function setting(): Arguments
    {
        return glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
    }

    /**
     * The proxy servers that can send a proxy header.
     * Whip ignores every proxy header when both lists are empty.
     *
     * @return array{ipv4: string[], ipv6: string[]}
     */
    protected function trustedProxies(): array
    {
        $ranges = $this->ranges($this->setting()->sanitize('trusted_proxies', 'text-multiline'));
        $whitelist = glsr()->filterArray('whip/whitelist', [
            Whip::CUSTOM_HEADERS => $this->groupRanges($ranges),
        ]);
        $filtered = Arr::consolidate($whitelist[Whip::CUSTOM_HEADERS] ?? []);
        $ranges = array_merge(
            Arr::consolidate($filtered[Whip::IPV4] ?? []),
            Arr::consolidate($filtered[Whip::IPV6] ?? [])
        );
        $ranges = array_map('trim', array_filter($ranges, 'is_string'));
        return $this->groupRanges(array_filter($ranges, [$this, 'isRange']));
    }
}
