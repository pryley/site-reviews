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
        $this->trustedProxies = $request->sanitize('trusted_proxies', 'text-multiline');
    }

    public function handle(): void
    {
        $trustedProxies = glsr(ClientIp::class)->ranges($this->trustedProxies);
        update_option(glsr()->prefix.'ip_proxy', [
            'proxy_http_header' => $this->proxyHeader,
            'trusted_proxies' => implode("\n", $trustedProxies),
        ]);
        glsr(Notice::class)->clear()->addSuccess(
            _x('The proxy HTTP header has been saved.', 'admin-text', 'site-reviews')
        );
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
