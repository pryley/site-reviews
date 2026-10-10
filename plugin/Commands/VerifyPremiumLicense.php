<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;
use GeminiLabs\SiteReviews\Request;

/**
 * The "Verify License Key" button of the License Key row.
 */
class VerifyPremiumLicense extends AbstractPremiumCommand
{
    public array $check = [];
    public array $connect = [];
    public string $license;
    public array $notice = [];
    protected bool $connects;

    public function __construct(Request $request)
    {
        $this->connects = wp_validate_boolean($request->get('connect', true)); // premium/connect verifies without going on
        $this->license = sanitize_text_field((string) $request->get('license', ''));
    }

    public function handle(): void
    {
        if (!$this->canManage()) {
            return;
        }
        if ('' === $this->license) {
            $this->refuse('license_missing', _x('Please enter your license key.', 'admin-text', 'site-reviews'), [], 400, 'warning');
            return;
        }
        $updater = glsr(PremiumLicense::class)->updater($this->license, true);
        $this->check = $updater->checkLicense();
        if ('unknown' === $this->check['license']) {
            $this->refuse('license_server',
                _x('The license server could not be reached. Please try again later.', 'admin-text', 'site-reviews'),
                [], 503, 'warning'
            );
            return;
        }
        if (true !== $this->check['success']) {
            $this->refuse('license_invalid',
                _x('This appears to be an invalid license key for Site Reviews Premium.', 'admin-text', 'site-reviews'),
                $this->licenseKeysLink()
            );
            return;
        }
        if ('disabled' === $this->check['license']) {
            $this->refuse('license_disabled',
                _x('Your license key has been disabled. Please contact support for more information.', 'admin-text', 'site-reviews'),
                $this->supportLink()
            );
            return;
        }
        if (in_array($this->check['license'], ['invalid_item_id', 'item_name_mismatch'], true)) {
            $this->refuse('license_mismatch',
                _x('This license key is for a different product.', 'admin-text', 'site-reviews'),
                $this->link(glsr_premium_url('site-reviews-premium'), _x('Get Site Reviews Premium', 'admin-text', 'site-reviews'))
            );
            return;
        }
        if ('expired' === $this->check['license']) {
            $this->save();
            glsr(PremiumLicense::class)->flush();
            $this->redraw = true;
            $this->refuse('license_expired',
                _x('Your license has expired. Please renew it to use Site Reviews Premium.', 'admin-text', 'site-reviews'),
                $this->link(glsr_premium_url('license-keys'), _x('Renew your license', 'admin-text', 'site-reviews'))
            );
            return;
        }
        if ('valid' === $this->check['license']) {
            $this->save();
            $this->connect();
            return;
        }
        if (in_array($this->check['license'], ['inactive', 'site_inactive'], true) && PremiumLicense::hasActivationsLeft($this->check)) {
            $activation = $updater->activateLicense();
            if ('valid' === $activation['license']) {
                $this->check = array_replace($this->check, $activation, ['license' => 'valid']);
                $this->save();
                glsr(PremiumLicense::class)->flush(); // the cached check still says the site is inactive
                $this->connect();
                return;
            }
        }
        $this->refuse('license_inactive',
            _x('Your license key has reached its activation limit. Please visit the License Keys page of your Nifty Plugins account and click "Manage Sites" to activate it here.', 'admin-text', 'site-reviews'),
            $this->licenseKeysLink(), 400, 'warning'
        );
    }

    public function response(): array
    {
        $response = parent::response();
        if ($this->successful()) {
            $response = array_filter($response + ['connect' => $this->connect, 'notice' => $this->notice]);
        }
        return $response;
    }

    protected function connect(): void
    {
        $license = glsr(PremiumLicense::class);
        if (!$this->connects || PremiumLicense::STATE_ACTIVE !== $license->state() || !$license->canInstall()) {
            return;
        }
        $connect = new ConnectPremium($this->check);
        $connect->handle();
        if ($connect->successful()) {
            $this->connect = $connect->response();
            return;
        }
        $this->notice = array_filter([
            'code' => $connect->code,
            'link' => $connect->link,
            'message' => $connect->message,
            'type' => $connect->type,
        ]);
    }

    protected function save(): void
    {
        glsr(OptionManager::class)->set('settings.licenses.'.static::ADDON_ID, $this->license);
    }
}
