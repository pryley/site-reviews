<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\ClientIp;
use GeminiLabs\SiteReviews\Modules\Notice;

class DetectIpAddress extends AbstractCommand
{
    public function handle(): void
    {
        $ipAddress = Helper::clientIp();
        $link = glsr_admin_link('documentation.faq', _x('FAQ', 'admin-text', 'site-reviews'), '#faq-ipaddress-incorrect');
        if ('unknown' === $ipAddress) {
            glsr(Notice::class)->addWarning(sprintf(
                /* translators: %s: link to the FAQ page */
                _x('Site Reviews was unable to detect an IP address. To fix this, please see the %s.', 'admin-text', 'site-reviews'),
                $link
            ));
            return;
        }
        glsr(Notice::class)->addSuccess(sprintf(
            /* translators: %1$s: detected IP address, %2$s: link to the FAQ page */
            _x('Your detected IP address is %1$s. If this looks incorrect, please see the %2$s.', 'admin-text', 'site-reviews'),
            "<code>{$ipAddress}</code>",
            $link
        ));
        foreach ($this->warnings() as $warning) {
            glsr(Notice::class)->addWarning($warning);
        }
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }

    /**
     * @return string[] the reasons that the selected proxy header was not used
     */
    protected function warnings(): array
    {
        $clientIp = glsr(ClientIp::class);
        $header = $clientIp->proxyHeader();
        if (empty($header)) {
            return [];
        }
        $header = sprintf('<code>%s</code>', esc_html($clientIp->headers()[$header] ?? $header));
        $peer = sprintf('<code>%s</code>', esc_html($clientIp->peer()));
        $warnings = [];
        if (!$clientIp->isProxyHeaderSent()) {
            $warnings[] = sprintf(
                /* translators: %1$s: the name of the HTTP header, %2$s: IP address */
                _x('The %1$s header was not sent with this request which came from %2$s.', 'admin-text', 'site-reviews'),
                $header,
                $peer
            );
        }
        if (!$clientIp->hasTrustedProxies()) {
            $warnings[] = sprintf(
                /* translators: %1$s: the name of the HTTP header, %2$s: IP address */
                _x('The %1$s header is ignored because Trusted Proxies is empty. This request came from %2$s.', 'admin-text', 'site-reviews'),
                $header,
                $peer
            );
        } elseif (!$clientIp->isTrustedProxy($clientIp->peer())) {
            $warnings[] = sprintf(
                /* translators: %1$s: the name of the HTTP header, %2$s: IP address */
                _x('The %1$s header is ignored because this request came from %2$s which is not in Trusted Proxies.', 'admin-text', 'site-reviews'),
                $header,
                $peer
            );
        }
        return $warnings;
    }
}
