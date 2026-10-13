<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\Vectorface\Whip\IpRange\IpWhitelist;
use GeminiLabs\Vectorface\Whip\Whip;

/**
 * The IP address of the visitor who sent the request.
 */
class ClientIp
{
    public function address(): string
    {
        $trustedProxies = $this->trustedProxies();
        $whip = new Whip(Whip::REMOTE_ADDR | Whip::CUSTOM_HEADERS, [
            Whip::CUSTOM_HEADERS => $trustedProxies,
        ], $this->server($trustedProxies));
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

    public function hasTrustedProxies(): bool
    {
        $trustedProxies = $this->trustedProxies();
        return !empty($trustedProxies[Whip::IPV4]) || !empty($trustedProxies[Whip::IPV6]);
    }

    /**
     * Checks if the Trusted Proxies list was saved by version 8.3.4 or earlier.
     * A later version saves its version number with the list.
     */
    public function hasUnverifiedProxies(): bool
    {
        $setting = $this->setting();
        if (empty($this->lines($setting->sanitize('trusted_proxies', 'text-multiline')))) {
            return false;
        }
        return empty($setting->sanitize('version', 'version'));
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
     * Checks that the request has the selected proxy header.
     */
    public function isProxyHeaderSent(): bool
    {
        $value = $_SERVER[$this->serverKey()] ?? '';
        return is_string($value) && '' !== trim($value);
    }

    public function isTrustedProxy(string $address): bool
    {
        if (false === filter_var($address, \FILTER_VALIDATE_IP)) {
            return false;
        }
        return (new IpWhitelist($this->trustedProxies()))->isIpWhitelisted($address);
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
     * E.g. "HTTP_X_REAL_IP" is "x-real-ip".
     */
    public function normalizeHeader(string $header): string
    {
        $header = Str::removePrefix(strtolower(trim($header)), 'http_');
        return glsr(Sanitizer::class)->sanitizeId(str_replace('_', '-', $header));
    }

    /**
     * Returns the IP address that connected to the web server, or an empty string.
     */
    public function peer(): string
    {
        $address = $_SERVER['REMOTE_ADDR'] ?? '';
        return false !== filter_var($address, \FILTER_VALIDATE_IP) ? $address : '';
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

    protected function entryAddress(string $entry): string
    {
        $entry = trim($entry);
        // The "for" parameter of a Forwarded entry, with or without quotes: for="203.0.113.5"
        if (1 === preg_match('/(?:^|;)\s*for\s*=\s*"?([^";]*)/i', $entry, $matches)) {
            $entry = trim($matches[1]);
        }
        // An address in brackets, with or without a port: [2001:db8::7]:4711
        if (1 === preg_match('/^\[([^\]]*)\](?::\d+)?$/', $entry, $matches)) {
            return $matches[1];
        }
        // An IPv4 address with a port: 203.0.113.5:4711
        if (1 === preg_match('/^(\d{1,3}(?:\.\d{1,3}){3}):\d+$/', $entry, $matches)) {
            return $matches[1];
        }
        return $entry;
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
     * Checks that an IP address connected to the web server (WP-CLI will return false).
     */
    protected function hasPeer(): bool
    {
        return '' !== $this->peer();
    }

    /**
     * Checks that a range is an IP address or CIDR range with a valid mask.
     * Whip does not validate the mask.
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

    /**
     * Returns a copy of $_SERVER with the proxy header changed to hold only the address
     * of the visitor. Whip uses the first address in a proxy header which is not always
     * the visitor.
     *
     * @param array{ipv4: string[], ipv6: string[]} $trustedProxies
     */
    protected function server(array $trustedProxies): array
    {
        $server = $_SERVER;
        $key = $this->serverKey();
        if (!is_string($server[$key] ?? null) || !$this->hasPeer()) {
            return $server;
        }
        $proxies = new IpWhitelist($trustedProxies);
        if ($proxies->isIpWhitelisted($server['REMOTE_ADDR'])) {
            $server[$key] = $this->visitorAddress($server[$key], $proxies);
        }
        return $server;
    }

    /**
     * Returns the $_SERVER key of the selected proxy header: "x-real-ip" is "HTTP_X_REAL_IP".
     */
    protected function serverKey(): string
    {
        return 'HTTP_'.strtoupper(str_replace('-', '_', $this->proxyHeader()));
    }

    protected function setting(): Arguments
    {
        return glsr()->args(get_option(glsr()->prefix.'ip_proxy'));
    }

    /**
     * Returns the proxy servers that a HTTP header can be read from.
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

    /**
     * Returns the visitor address from the header.
     */
    protected function visitorAddress(string $header, IpWhitelist $proxies): string
    {
        $entries = array_map([$this, 'entryAddress'], explode(',', $header));
        $entries = array_values(array_filter($entries, 'strlen'));
        foreach (array_reverse($entries) as $entry) {
            if (false === filter_var($entry, \FILTER_VALIDATE_IP) || !$proxies->isIpWhitelisted($entry)) {
                return $entry;
            }
        }
        return $entries[0] ?? '';
    }
}
