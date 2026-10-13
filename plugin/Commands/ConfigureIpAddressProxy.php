<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Modules\ClientIp;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ConfigureIpAddressProxy extends AbstractCommand
{
    protected string $proxyHeader;
    protected string $trustedProxies;

    public function __construct(Request $request)
    {
        $this->proxyHeader = $request->sanitize('proxy_http_header', 'id');
        if ('custom' === $this->proxyHeader) {
            $this->proxyHeader = glsr(ClientIp::class)->normalizeHeader(
                $request->sanitize('custom_http_header', 'text')
            );
        }
        $this->trustedProxies = $request->sanitize('trusted_proxies', 'text-multiline');
    }

    public function handle(): void
    {
        $clientIp = glsr(ClientIp::class);
        $trustedProxies = $clientIp->ranges($this->trustedProxies);
        update_option(glsr()->prefix.'ip_proxy', [
            'proxy_http_header' => $this->proxyHeader,
            'trusted_proxies' => implode("\n", $trustedProxies),
            'version' => glsr()->version,
        ]);
        glsr(Notice::class)->clear()->addSuccess(
            _x('The proxy HTTP header has been saved.', 'admin-text', 'site-reviews')
        );
        $removed = count($clientIp->lines($this->trustedProxies)) - count($trustedProxies);
        if ($removed > 0) {
            glsr(Notice::class)->addWarning(sprintf(
                /* translators: %s: number of lines */
                _nx('%s line was removed from Trusted Proxies because it is not a valid IP address or CIDR range.', '%s lines were removed from Trusted Proxies because they are not valid IP addresses or CIDR ranges.', $removed, 'admin-text', 'site-reviews'),
                number_format_i18n($removed)
            ));
        }
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
