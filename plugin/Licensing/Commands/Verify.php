<?php

namespace GeminiLabs\SiteReviews\Licensing\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Licensing\Check;
use GeminiLabs\SiteReviews\Licensing\LicenseRow;
use GeminiLabs\SiteReviews\Licensing\LicenseServer;
use GeminiLabs\SiteReviews\Request;

/**
 * The "Verify License Key" button of the License Key row.
 */
class Verify extends AbstractCommand
{
    public ?Check $check = null;
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
        // A person asked: ask the server now.
        $this->check = glsr(LicenseServer::class)->check(static::ADDON_ID, $this->license, true);
        $method = Helper::buildMethodName('onStatus', $this->check->verdict->value);
        if (!method_exists($this, $method)) {
            $method = 'onStatusExhausted'; // unknown status fallback
        }
        $this->$method();
    }

    public function response(): array
    {
        $response = parent::response();
        if ($this->successful()) {
            $response = array_filter($response + [
                'connect' => $this->connect,
                'notice' => $this->notice,
            ]);
        }
        return $response;
    }

    protected function connect(): void
    {
        if (!$this->connects) {
            return;
        }
        $license = glsr(LicenseRow::class);
        if (LicenseRow::STATE_ACTIVE !== $license->state() || !$license->canInstall()) {
            return;
        }
        $connect = new Authorize($this->check);
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

    protected function onStatusDisabled(): void
    {
        $this->refuse('license_disabled',
            _x('Your license key has been disabled. Please contact support for more information.', 'admin-text', 'site-reviews'),
            $this->supportLink()
        );
    }

    protected function onStatusExhausted(): void
    {
        $this->refuse('license_inactive',
            _x('Your license key has reached its activation limit. Please visit the License Keys page of your account and click "Manage Sites" to activate it here.', 'admin-text', 'site-reviews'),
            $this->licenseKeysLink(), 400, 'warning'
        );
    }

    protected function onStatusExpired(): void
    {
        $this->save();
        glsr(LicenseRow::class)->flush();
        $this->redraw = true;
        $this->refuse('license_expired',
            _x('Your license has expired. Please renew it to use Site Reviews Premium.', 'admin-text', 'site-reviews'),
            $this->link(glsr_premium_url('license-keys'), _x('Renew your license', 'admin-text', 'site-reviews'))
        );
    }

    protected function onStatusInactive(): void
    {
        $activation = glsr(LicenseServer::class)->activate(static::ADDON_ID, $this->license);
        if (!$activation->isValid()) {
            $this->onStatusExhausted();
            return;
        }
        // The activation answer lacks the connect URL and the premium flag
        $this->check = new Check(array_replace($this->check->toArray(), $activation->toArray(), ['license' => 'valid']));
        $this->save();
        glsr(LicenseRow::class)->flush(); // the cached check still says the site is inactive
        $this->connect();
    }

    protected function onStatusInvalid(): void
    {
        $this->refuse('license_invalid',
            _x('This appears to be an invalid license key for Site Reviews Premium.', 'admin-text', 'site-reviews'),
            $this->licenseKeysLink()
        );
    }

    protected function onStatusMismatch(): void
    {
        $this->refuse('license_mismatch',
            _x('This license key is for a different product.', 'admin-text', 'site-reviews'),
            $this->link(glsr_premium_url('site-reviews-premium'), _x('Get Site Reviews Premium', 'admin-text', 'site-reviews'))
        );
    }

    protected function onStatusUnreachable(): void
    {
        $this->refuse('license_server',
            _x('The license server could not be reached. Please try again later.', 'admin-text', 'site-reviews'),
            [], 503, 'warning'
        );
    }

    protected function onStatusValid(): void
    {
        $this->save();
        $this->connect();
    }

    protected function save(): void
    {
        glsr(OptionManager::class)->set('settings.licenses.'.static::ADDON_ID, $this->license);
    }
}
