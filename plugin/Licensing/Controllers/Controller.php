<?php

namespace GeminiLabs\SiteReviews\Licensing\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Licensing\Commands\Install;
use GeminiLabs\SiteReviews\Licensing\Commands\ValidateLicenseKeys;
use GeminiLabs\SiteReviews\Licensing\License;
use GeminiLabs\SiteReviews\Licensing\LicenseRow;
use GeminiLabs\SiteReviews\Modules\Notice;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/addon/submenu/pages
     */
    public function filterSubmenuPages(array $pages): array
    {
        if (!isset($pages['premium']) || glsr(LicenseRow::class)->isOnDisk()) {
            return $pages;
        }
        if ('' !== glsr(License::class)->premiumKey()) {
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

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function sanitizeLicenses(array $options, array $input): array
    {
        $key = 'settings.licenses';
        $command = new ValidateLicenseKeys(
            Arr::consolidate(Arr::get($options, $key)),
            Arr::consolidate(Arr::get($input, $key))
        );
        $this->execute($command);
        return Arr::set($options, $key, $command->licenses);
    }
}
