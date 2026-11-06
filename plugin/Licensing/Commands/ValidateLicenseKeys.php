<?php

namespace GeminiLabs\SiteReviews\Licensing\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand as AbstractBaseCommand;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Licensing\Check;
use GeminiLabs\SiteReviews\Licensing\LicenseServer;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Notice;

/**
 * The keys posted from the Licenses tab: invalid keys are dropped from the saved settings.
 */
class ValidateLicenseKeys extends AbstractBaseCommand
{
    public array $licenses;
    protected string $addonId = '';
    protected ?Check $check = null;
    protected string $license = '';
    protected array $posted;

    public function __construct(array $saved, array $posted)
    {
        $this->licenses = array_replace($saved, $posted); // keep saved keys that do not have a field in the form
        $this->posted = array_filter($posted);
    }

    public function handle(): void
    {
        foreach ($this->posted as $addonId => $license) {
            $this->addonId = $addonId;
            $this->license = $license;
            // A person saved the key: ask the server now.
            $this->check = glsr(LicenseServer::class)->check($addonId, $license, true);
            $method = Helper::buildMethodName('onStatus', $this->check->verdict->value);
            if (!method_exists($this, $method)) {
                $method = 'onStatusExhausted'; // unknown status fallback
            }
            $this->$method();
        }
    }

    protected function drop(): void
    {
        $this->licenses[$this->addonId] = '';
    }

    protected function notice(string $error, string $message = ''): void
    {
        $text = '' === $message ? $error : sprintf('<strong>%s</strong><br>%s', $error, $message);
        glsr(Notice::class)->addError($text);
    }

    protected function onStatusDisabled(): void
    {
        glsr_log()->error("Disabled license: {$this->license} ({$this->addonId})");
        $this->notice(
            _x('A license you entered has been disabled.', 'admin-text', 'site-reviews'),
            /* translators: %s: link with the text "contact support" */
            sprintf(_x('Please %s for more information.', 'admin-text', 'site-reviews'),
                glsr_premium_link('support', _x('contact support', 'admin-text', 'site-reviews'))
            )
        );
        $this->drop();
    }

    protected function onStatusExhausted(): void
    {
        $this->notice(
            _x('A license you entered has not been activated for your website.', 'admin-text', 'site-reviews'),
            /* translators: %s: link to the License Keys page */
            sprintf(_x('To activate your license, please visit the %s page on your Nifty Plugins account and click the "Manage Sites" button to activate it for your website.', 'link to License Keys page (admin-text)', 'site-reviews'),
                glsr_premium_link('license-keys')
            )
        );
        $this->drop();
    }

    protected function onStatusExpired(): void
    {
        $this->notice(
            /* translators: %s: the relative expiry date (e.g. 3 days ago) */
            sprintf(_x('A license you entered has expired: %s.', 'admin-text', 'site-reviews'),
                glsr(Date::class)->relative($this->check->expires)
            ),
            /* translators: %s: link to the License Keys page */
            sprintf(_x('To renew your license and enable updates, please visit the %s on your Nifty Plugins account.', 'link to License Keys page (admin-text)', 'site-reviews'),
                glsr_premium_link('license-keys')
            )
        );
    }

    protected function onStatusInactive(): void
    {
        $activation = glsr(LicenseServer::class)->activate($this->addonId, $this->license);
        if (!$activation->isValid()) {
            glsr_log($activation->toArray());
            $this->drop();
            return;
        }
        glsr(Notice::class)->addSuccess('A license you entered has been activated for this website.');
    }

    protected function onStatusInvalid(): void
    {
        glsr_log()->error("Invalid license: {$this->license} ({$this->addonId})");
        $this->notice(_x('A license you entered is invalid.', 'admin-text', 'site-reviews'));
        $this->drop();
    }

    protected function onStatusMismatch(): void
    {
        $this->notice(
            _x('A license you entered is for a different product.', 'admin-text', 'site-reviews'),
            _x('Please enter each license key in the field of the addon it was purchased for.', 'admin-text', 'site-reviews')
        );
        $this->drop();
    }

    protected function onStatusUnreachable(): void
    {
        glsr(Notice::class)->addWarning(
            _x('The license server could not be reached. Please try again later.', 'admin-text', 'site-reviews')
        );
        $this->licenses[$this->addonId] = glsr(OptionManager::class)->get("settings.licenses.{$this->addonId}", '', 'string');
    }

    protected function onStatusValid(): void
    {
    }
}
