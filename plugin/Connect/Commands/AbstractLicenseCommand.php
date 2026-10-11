<?php

namespace GeminiLabs\SiteReviews\Connect\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Connect\LicenseRow;

abstract class AbstractLicenseCommand extends AbstractCommand
{
    public const ADDON_ID = LicenseRow::ADDON_ID;

    public string $code = '';
    public array $link = [];
    public string $message = '';
    public bool $redraw = false;
    public int $status = 200;
    public string $type = 'error';

    public function response(): array
    {
        if (!$this->successful()) {
            return array_filter([
                'code' => $this->code,
                'html' => $this->redraw ? glsr(LicenseRow::class)->render() : '',
                'link' => $this->link,
                'message' => $this->message,
                'type' => $this->type,
            ]);
        }
        return [
            'html' => glsr(LicenseRow::class)->render(),
            'state' => glsr(LicenseRow::class)->state(),
        ];
    }

    protected function canManage(): bool
    {
        if (!current_user_can(glsr()->getPermission('settings', 'licenses'))) {
            return $this->refuse('cannot_manage',
                _x('You are not allowed to manage license keys on this site.', 'admin-text', 'site-reviews'), [], 403
            );
        }
        return true;
    }

    protected function licenseKeysLink(): array
    {
        return $this->link(glsr_premium_url('license-keys'), _x('Open the License Keys page of your account', 'admin-text', 'site-reviews'));
    }

    protected function link(string $url, string $text): array
    {
        return compact('text', 'url');
    }

    protected function manualInstallLink(): array
    {
        return $this->link(glsr_premium_url('account'), _x('Download Site Reviews Premium from your account and install it manually', 'admin-text', 'site-reviews'));
    }

    protected function refuse(string $code, string $message, array $link = [], int $status = 400, string $type = 'error'): bool
    {
        $this->code = glsr()->prefix.$code;
        $this->link = $link;
        $this->message = $message;
        $this->status = $status;
        $this->type = $type;
        $this->fail();
        return false;
    }

    protected function supportLink(): array
    {
        return $this->link(glsr_premium_url('support'), _x('Contact support', 'admin-text', 'site-reviews'));
    }
}
