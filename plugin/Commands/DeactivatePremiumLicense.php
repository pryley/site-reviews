<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;
use GeminiLabs\SiteReviews\Request;

/**
 * The "Deactivate" and "Delete" buttons of the License Key row.
 */
class DeactivatePremiumLicense extends AbstractPremiumCommand
{
    public bool $delete;

    public function __construct(Request $request)
    {
        $this->delete = wp_validate_boolean($request->get('delete', false));
    }

    public function handle(): void
    {
        if (!$this->canManage()) {
            return;
        }
        $license = glsr(PremiumLicense::class);
        $key = $license->savedKey();
        if ('' === $key) {
            $this->refuse('license_missing', _x('No license key is saved.', 'admin-text', 'site-reviews'), [], 400, 'warning');
            return;
        }
        if (!$this->delete) {
            $license->updater($key, true)->deactivateLicense();
        }
        $license->flush();
        if ($this->delete) {
            glsr(OptionManager::class)->set('settings.licenses.'.static::ADDON_ID, '');
        }
    }
}
