<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;

/**
 * The "License Key" row at the top of Settings > General.
 */
class LicenseRow
{
    public const ADDON_ID = 'site-reviews-premium';
    public const PLUGIN_FILE = 'site-reviews-premium/site-reviews-premium.php';

    public const STATE_INACTIVE = 1; // no key, or an invalid saved key
    public const STATE_ACTIVE = 2; // a valid key, premium not on disk
    public const STATE_ON_DISK = 3; // a valid key, premium on disk and inactive
    public const STATE_INSTALLED = 4; // a valid key, premium registered

    protected ?array $check = null;
    protected string $checkedKey = '';

    /**
     * The Plugins screen's activation link, not the Gatekeeper notice's silent one.
     */
    public function activateUrl(): string
    {
        $url = add_query_arg([
            'action' => 'activate',
            'plugin' => static::PLUGIN_FILE,
        ], self_admin_url('plugins.php'));
        return wp_nonce_url($url, 'activate-plugin_'.static::PLUGIN_FILE);
    }

    /**
     * The names of the addons replaced by premium.
     */
    public function addonNames(): array
    {
        $names = [];
        foreach (array_keys(glsr()->retrieveAs('array', 'licensed', [])) as $addonId) {
            if (static::ADDON_ID !== $addonId && null !== glsr()->addon($addonId)) {
                $names[] = glsr($addonId)->name;
            }
        }
        natcasesort($names);
        return array_values($names);
    }

    public function canInstall(): bool
    {
        foreach (['install_plugins', 'activate_plugins', 'manage_options'] as $capability) {
            if (!current_user_can($capability)) {
                return false;
            }
        }
        return wp_is_file_mod_allowed('site_reviews_premium_install');
    }

    public function check(bool $force = false): array
    {
        $key = $this->savedKey();
        if ('' === $key) {
            return [];
        }
        if ($force || $this->isStale($key)) {
            $this->check = $this->updater($key, $force)->checkLicense(); // the row reads it several times
            $this->checkedKey = $key;
        }
        return $this->check;
    }

    public function flush(): void
    {
        $this->check = null;
        $updater = $this->updater($this->savedKey());
        glsr(Api::class, ['url' => $updater->apiUrl])->flushAll('check_license');
        $updater->flushCachedVersion();
    }

    public function isActive(): bool
    {
        return 'valid' === ($this->check()['license'] ?? '');
    }

    public function isInstalled(): bool
    {
        return !is_null(glsr()->addon(static::ADDON_ID));
    }

    public function isOnDisk(): bool
    {
        return file_exists(\WP_PLUGIN_DIR.'/'.static::PLUGIN_FILE);
    }

    public function key(): string
    {
        $saved = $this->savedKey();
        return '' !== $saved ? $saved : glsr(License::class)->premiumKey();
    }

    /**
     * Readonly while the key is active so the settings form still submits it.
     */
    public function keyField(): SettingField
    {
        $args = [
            'class' => 'regular-text',
            'id' => 'glsr-premium-license-key',
            'label' => _x('License Key', 'admin-text', 'site-reviews'),
            'name' => 'settings.licenses.'.static::ADDON_ID,
            'placeholder' => _x('Paste license key', 'admin-text', 'site-reviews'),
            /* translators: %s: link to the License Keys page */
            'tooltip' => sprintf(_x('Enter the license key here. Your license can be found on the %s page of your Nifty Plugins account.', 'link to License Keys page (admin-text)', 'site-reviews'),
                glsr_premium_link('license-keys')
            ),
            'type' => 'secret',
            'value' => $this->key(),
        ];
        if ($this->isActive()) {
            $args['readonly'] = true;
        }
        return new SettingField($args);
    }

    /**
     * The text under the field.
     *
     * @return array{text: string, type: string}
     */
    public function message(): array
    {
        $check = $this->check();
        if (empty($check)) {
            return $this->isInstalled() || '' !== $this->key()
                ? $this->text('') // the section notice says it
                : $this->text(_x('Already purchased? Enter your license key to enable Site Reviews Premium.', 'admin-text', 'site-reviews'), 'description');
        }
        $status = LicenseStatus::of($check);
        if (LicenseStatus::VALID === $status && $this->expiresSoon($check)) {
            $status = 'expiring';
        }
        $messages = $this->messages($check);
        $message = $messages[$status] ?? $messages[LicenseStatus::INVALID];
        return $message();
    }

    /**
     * The notice above the row.
     *
     * @return array{text: string, type: string}
     */
    public function notice(): array
    {
        $addons = $this->addonNames();
        $names = wp_sprintf_l('%l', $addons);
        $replaces = empty($addons) ? '' : sprintf(
            /* translators: %s: the names of the active addons */
            _nx('It replaces %s with one plugin and imports its settings.', 'It replaces %s with one plugin and imports their settings.', count($addons), 'admin-text', 'site-reviews'),
            $names
        );
        $sentence = fn (string $first, string $second = '') => trim($first.' '.$second);
        $link = sprintf('<a href="%s">%s</a>', esc_url(glsr_admin_url('premium')), _x('Site Reviews Premium', 'admin-text', 'site-reviews'));
        $notices = [
            'installed' => fn () => $this->text(''),
            'on-disk' => fn () => $this->text($sentence(_x('Site Reviews Premium is installed but not active. Activate it to unlock all features.', 'admin-text', 'site-reviews'), $replaces), 'info'),
            'install' => fn () => $this->text($sentence(_x('Your license is active. Install Site Reviews Premium to unlock all features.', 'admin-text', 'site-reviews'), $replaces), 'info'),
            'download' => fn () => $this->text($sentence(sprintf(
                /* translators: %s: link to the Nifty Plugins account page */
                _x('Your license is active. Download Site Reviews Premium from %s and upload it on the Plugins screen.', 'admin-text', 'site-reviews'),
                glsr_premium_link('account', _x('your account', 'admin-text', 'site-reviews'))
            ), $replaces), 'info'),
            'unreachable' => fn () => $this->text(''), // nothing is known: the message under the field says so
            'unlicensed' => fn () => $this->text(_x('Site Reviews Premium is not receiving updates because it has no valid license key. Verify your license key to receive updates again.', 'admin-text', 'site-reviews'), 'warning'),
            'refused' => fn () => $this->text($sentence(
                _x('Site Reviews Premium cannot be installed until a valid license key is verified.', 'admin-text', 'site-reviews'),
                /* translators: %s: the names of the active addons */
                empty($addons) ? '' : sprintf(_x('You are using %s, which it replaces.', 'admin-text', 'site-reviews'), $names)
            ), 'warning'),
            'flagged' => fn () => $this->text(_x('Your license includes Site Reviews Premium. Verify your license key to install it.', 'admin-text', 'site-reviews'), 'info'),
            'flagged-addons' => fn () => $this->text(sprintf(
                /* translators: %s: the names of the active addons */
                _nx('You are using %s, and your license includes Site Reviews Premium, which replaces it. Verify your license key to install it.', 'You are using %s, and your license includes Site Reviews Premium, which replaces them with one plugin. Verify your license key to install it.', count($addons), 'admin-text', 'site-reviews'),
                $names
            ), 'info'),
            'upsell-addons' => fn () => $this->text(sprintf(
                /* translators: %1$s: the names of the active addons, %2$s: link with the text "Site Reviews Premium" */
                _nx('You are using %1$s. Site Reviews Premium replaces it and adds more features: consider upgrading to %2$s.', 'You are using %1$s. Site Reviews Premium replaces them with one plugin and adds more features: consider upgrading to %2$s.', count($addons), 'admin-text', 'site-reviews'),
                $names,
                $link
            ), 'info'),
            'upsell' => fn () => $this->text(sprintf(
                /* translators: %s: link with the text "Site Reviews Premium" */
                _x('You are using the free Site Reviews plugin. To unlock more features, consider upgrading to %s.', 'admin-text', 'site-reviews'),
                $link
            ), 'info'),
        ];
        return $notices[$this->noticeCase($addons)]();
    }

    public function render(): string
    {
        $field = $this->keyField();
        $status = $this->check()['license'] ?? '';
        return glsr()->build('partials/settings/premium-license', [
            'activate_url' => $this->activateUrl(),
            'can_delete' => '' !== $this->savedKey() && 'unknown' !== $status,
            'can_install' => $this->canInstall(),
            'field' => $field->buildFieldElement(),
            'label' => $field->buildFieldLabel(),
            'message' => $this->message(),
            'notice' => $this->notice(),
            'state' => $this->state(),
        ]);
    }

    public function savedKey(): string
    {
        return glsr_get_option('licenses.'.static::ADDON_ID, '', 'string');
    }

    public function state(): int
    {
        if (!$this->isActive()) {
            return static::STATE_INACTIVE;
        }
        if ($this->isInstalled()) {
            return static::STATE_INSTALLED;
        }
        if ($this->isOnDisk()) {
            return static::STATE_ON_DISK;
        }
        return static::STATE_ACTIVE;
    }

    public function updater(string $key, bool $force = false): Updater
    {
        return new Updater(static::ADDON_ID, [
            'force' => $force,
            'license' => $key,
        ]);
    }

    protected function date(string $date): string
    {
        return date_i18n(get_option('date_format'), strtotime($date));
    }

    protected function expiresSoon(array $check): bool
    {
        $expires = strtotime($check['expires']);
        return !$check['lifetime'] && $expires > time() && $expires - time() < 30 * DAY_IN_SECONDS;
    }

    protected function expiryMessage(array $check): string
    {
        if ($check['lifetime']) {
            return _x('License key never expires.', 'admin-text', 'site-reviews');
        }
        /* translators: %s: the expiry date */
        return sprintf(_x('Your license key expires on %s.', 'admin-text', 'site-reviews'), $this->date($check['expires']));
    }

    protected function isStale(string $key): bool
    {
        return null === $this->check || $key !== $this->checkedKey;
    }

    /**
     * The text under the field for each status.
     *
     * @return array<string, callable(): array{text: string, type: string}>
     */
    protected function messages(array $check): array
    {
        $subscription = $this->subscriptionMessage($check['subscription']);
        return [
            LicenseStatus::UNREACHABLE => fn () => $this->text(_x('The license server could not be reached. Please try again later.', 'admin-text', 'site-reviews'), 'warning'),
            LicenseStatus::EXPIRED => fn () => $this->text(sprintf(
                /* translators: %1$s: the expiry date, %2$s: the renewal link's opening tag, %3$s: its closing tag */
                _x('Your license key expired on %1$s. Please %2$srenew it%3$s.', 'admin-text', 'site-reviews'),
                $this->date($check['expires']),
                sprintf('<a href="%s" target="_blank">', esc_url('' !== $check['renewal_url'] ? $check['renewal_url'] : glsr_premium_url('license-keys'))),
                '</a>'
            ), 'error'),
            LicenseStatus::INACTIVE => fn () => $this->text(_x('Your license key is not active for this website. Click Verify License Key to activate it.', 'admin-text', 'site-reviews'), 'warning'),
            LicenseStatus::EXHAUSTED => fn () => $this->text(sprintf(
                /* translators: %s: link to the License Keys page */
                _x('Your license key has reached its activation limit. Please visit the %s page of your Nifty Plugins account and click "Manage Sites".', 'link to License Keys page (admin-text)', 'site-reviews'),
                glsr_premium_link('license-keys')
            ), 'warning'),
            LicenseStatus::DISABLED => fn () => $this->text(sprintf(
                /* translators: %s: link with the text "contact support" */
                _x('Your license key has been disabled. Please %s for more information.', 'admin-text', 'site-reviews'),
                glsr_premium_link('support', _x('contact support', 'admin-text', 'site-reviews'))
            ), 'error'),
            LicenseStatus::INVALID => fn () => $this->text(_x('This appears to be an invalid license key for Site Reviews Premium.', 'admin-text', 'site-reviews'), 'error'),
            /* translators: %s: the expiry date */
            'expiring' => fn () => $this->text(trim(sprintf(_x('Your license key expires soon! It expires on %s.', 'admin-text', 'site-reviews'), $this->date($check['expires'])).' '.$subscription), 'warning'),
            LicenseStatus::VALID => fn () => $this->text(trim($this->expiryMessage($check).' '.$subscription), 'description'),
        ];
    }

    /**
     * The notice that the row displays above the field.
     */
    protected function noticeCase(array $addons): string
    {
        $state = $this->state();
        if (static::STATE_INSTALLED === $state) {
            return 'installed';
        }
        if (static::STATE_ON_DISK === $state) {
            return 'on-disk';
        }
        if (static::STATE_ACTIVE === $state) {
            return $this->canInstall() ? 'install' : 'download';
        }
        $status = $this->check()['license'] ?? '';
        if ('unknown' === $status) {
            return 'unreachable';
        }
        if ($this->isInstalled()) {
            return 'unlicensed';
        }
        if ('' !== $status) {
            return 'refused';
        }
        if ('' !== $this->key()) { // the flagged key's addon is licensed but not registered (version-gated)
            return empty($addons) ? 'flagged' : 'flagged-addons';
        }
        return empty($addons) ? 'upsell' : 'upsell-addons';
    }

    protected function subscriptionMessage(string $subscription): string
    {
        if ('' === $subscription) {
            return '';
        }
        if ('active' === $subscription) {
            return _x('Your license subscription is active and will automatically renew.', 'admin-text', 'site-reviews');
        }
        $labels = [
            'cancelled' => _x('cancelled', 'subscription status (admin-text)', 'site-reviews'),
            'completed' => _x('completed', 'subscription status (admin-text)', 'site-reviews'),
            'expired' => _x('expired', 'subscription status (admin-text)', 'site-reviews'),
            'failing' => _x('failing', 'subscription status (admin-text)', 'site-reviews'),
            'pending' => _x('pending', 'subscription status (admin-text)', 'site-reviews'),
            'trialling' => _x('trialling', 'subscription status (admin-text)', 'site-reviews'),
        ];
        /* translators: %s: the subscription status */
        return sprintf(_x('Your license subscription is %s and will not automatically renew.', 'admin-text', 'site-reviews'), $labels[$subscription] ?? $subscription);
    }

    /**
     * @return array{text: string, type: string}
     */
    protected function text(string $text, string $type = 'description'): array
    {
        return compact('text', 'type');
    }
}
