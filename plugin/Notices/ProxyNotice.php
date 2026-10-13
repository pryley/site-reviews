<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Modules\ClientIp;

class ProxyNotice extends AbstractNotice
{
    protected string $type = 'notice-warning';

    protected function canLoad(): bool
    {
        if (!parent::canLoad()) {
            return false;
        }
        $clientIp = glsr(ClientIp::class);
        if ($clientIp->hasTrustedProxies()) {
            return false;
        }
        // A code snippet can add a proxy header to Whip without saving one in the tool.
        return !empty($clientIp->proxyHeader())
            || false !== has_action(glsr()->hookPrefix().'/whip');
    }

    protected function data(): array
    {
        $clientIp = glsr(ClientIp::class);
        $header = $clientIp->proxyHeader();
        return [
            'header' => $clientIp->headers()[$header] ?? $header,
        ];
    }

    /**
     * The notice links to the tool so it needs the same permission.
     */
    protected function hasPermission(): bool
    {
        return glsr()->hasPermission('tools', 'general');
    }

    protected function isDismissible(): bool
    {
        return false;
    }
}
