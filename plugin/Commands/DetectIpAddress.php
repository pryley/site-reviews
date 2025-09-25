<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Notice;

class DetectIpAddress extends AbstractCommand
{
    public function handle(): void
    {
        $ipAddress = Helper::getIpAddress();
        $link = glsr_admin_link('documentation.faq', _x('FAQ', 'admin-text', 'site-reviews'), '#faq-ipaddress-incorrect');
        if ('unknown' === $ipAddress) {
            glsr(Notice::class)->addWarning(sprintf(
                _x('Site Reviews was unable to detect an IP address. To fix this, please see the %s.', 'admin-text', 'site-reviews'),
                $link
            ));
            return;
        }
        glsr(Notice::class)->addSuccess(sprintf(
            _x('Your detected IP address is %s. If this looks incorrect, please see the %s.', 'admin-text', 'site-reviews'),
            "<code>{$ipAddress}</code>",
            $link
        ));
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
