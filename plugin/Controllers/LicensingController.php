<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Notice;

class LicensingController extends AbstractController
{
    /**
     * @filter site-reviews/settings/sanitize
     */
    public function sanitizeLicenses(array $options, array $input): array
    {
        $key = 'settings.licenses';
        $licenses = Arr::consolidate(Arr::get($input, $key));
        foreach ($licenses as $addonId => $license) {
            if (empty($license)) {
                continue;
            }
            $updater = new Updater($addonId, [
                'force' => true,
                'license' => $license,
            ]);
            if (!$this->isLicenseValid($updater)) {
                $licenses[$addonId] = '';
            }
        }
        $options = Arr::set($options, $key, $licenses);
        return $options;
    }

    protected function activateLicense(Updater $updater): bool
    {
        $check = $updater->activateLicense();
        if ('valid' !== $check['license']) {
            glsr_log($check);
            return false;
        }
        glsr(Notice::class)->addSuccess('A license you entered has been activated for this website.');
        return true;
    }

    protected function isLicenseValid(Updater $updater): bool
    {
        $check = $updater->checkLicense();
        if (true !== $check['success'] || 'disabled' === $check['license']) {
            glsr_log()->error("Invalid license: {$updater->license} ({$updater->addonId})");
            $this->renderInvalidLicenseNotice();
            return false;
        }
        if ('valid' === $check['license']) {
            return true;
        }
        if ('expired' === $check['license']) {
            $this->renderExpiredLicenseNotice($check['expires']);
            return true; // don't remove expired licenses from the settings
        }
        if (in_array($check['license'], ['inactive', 'site_inactive']) && $check['activations_left'] > 0) {
            return $this->activateLicense($updater);
        }
        $this->renderGenericLicenseNotice();
        return false;
    }

    protected function renderExpiredLicenseNotice(string $expiryDate): void
    {
        $error = sprintf(_x('A license you entered has expired: %s.', 'admin-text', 'site-reviews'),
            glsr(Date::class)->relative($expiryDate)
        );
        $message = sprintf(_x('To renew your license and enable updates, please visit the %s on your Nifty Plugins account.', 'admin-text', 'site-reviews'),
            sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
        );
        glsr(Notice::class)->addError(sprintf('<strong>%s</strong><br>%s', $error, $message));
    }

    protected function renderGenericLicenseNotice(): void
    {
        $error = _x('A license you entered has not been activated for your website.', 'admin-text', 'site-reviews');
        $message = sprintf(_x('To activate your license, please visit the %s page on your Nifty Plugins account and click the "Manage Sites" button to activate it for your website.', 'admin-text', 'site-reviews'),
            sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
        );
        glsr(Notice::class)->addError(sprintf('<strong>%s</strong><br>%s', $error, $message));
    }

    protected function renderInvalidLicenseNotice(): void
    {
        glsr(Notice::class)->addError(
            _x('A license you entered is either invalid or has been revoked.', 'admin-text', 'site-reviews')
        );
    }
}
