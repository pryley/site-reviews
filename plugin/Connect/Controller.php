<?php

namespace GeminiLabs\SiteReviews\Connect;

use GeminiLabs\SiteReviews\Connect\Commands\Install;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Notice;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/addon/submenu/pages
     */
    public function filterSubmenuPages(array $pages): array
    {
        if (isset($pages['premium']) && '' !== glsr(License::class)->premiumKey() && !glsr(LicenseRow::class)->isOnDisk()) {
            $pages['premium'] = _x('Install Premium', 'admin-text', 'site-reviews');
        }
        return $pages;
    }

    /**
     * @action load-site-review_page_glsr-settings
     */
    public function noticeInstalled(): void
    {
        if (false === get_transient(Install::INSTALLED_KEY)) {
            return;
        }
        delete_transient(Install::INSTALLED_KEY);
        if (glsr(LicenseRow::class)->isInstalled()) {
            glsr(Notice::class)->addSuccess(_x('Site Reviews Premium is installed and active.', 'admin-text', 'site-reviews'));
        }
    }
}
