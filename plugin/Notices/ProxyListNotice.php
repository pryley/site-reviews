<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Modules\ClientIp;

class ProxyListNotice extends AbstractNotice
{
    protected string $type = 'notice-warning';

    protected function canLoad(): bool
    {
        if (!parent::canLoad()) {
            return false;
        }
        $clientIp = glsr(ClientIp::class);
        if (empty($clientIp->proxyHeader())) {
            return false;
        }
        // ProxyNotice reports a list that has no valid line.
        return $clientIp->hasTrustedProxies() && $clientIp->hasUnverifiedProxies();
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
