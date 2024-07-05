<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_3_0_0 implements MigrateContract
{
    public const MAPPED_SETTINGS = [
        'settings.general.notification' => 'settings.general.notifications', // array
        'settings.general.notification_email' => 'settings.general.notification_email',
        'settings.general.notification_message' => 'settings.general.notification_message',
        'settings.general.require.approval' => 'settings.general.require.approval',
        'settings.general.require.login' => 'settings.general.require.login',
        'settings.general.require.login_register' => 'settings.general.require.login_register',
        'settings.general.webhook_url' => 'settings.general.notification_slack',
        'settings.reviews-form.akismet' => 'settings.submissions.akismet',
        'settings.reviews-form.blacklist.action' => 'settings.submissions.blacklist.action',
        'settings.reviews-form.blacklist.entries' => 'settings.submissions.blacklist.entries',
        'settings.reviews-form.recaptcha.integration' => 'settings.submissions.recaptcha.integration',
        'settings.reviews-form.recaptcha.key' => 'settings.submissions.recaptcha.key',
        'settings.reviews-form.recaptcha.position' => 'settings.submissions.recaptcha.position',
        'settings.reviews-form.recaptcha.secret' => 'settings.submissions.recaptcha.secret',
        'settings.reviews-form.required' => 'settings.submissions.required', // array
        'settings.reviews.assigned_links.enabled' => 'settings.reviews.assigned_links',
        'settings.reviews.avatars.enabled' => 'settings.reviews.avatars',
        'settings.reviews.date.custom' => 'settings.reviews.date.custom',
        'settings.reviews.date.format' => 'settings.reviews.date.format',
        'settings.reviews.excerpt.enabled' => 'settings.reviews.excerpts',
        'settings.reviews.excerpt.length' => 'settings.reviews.excerpts_length',
        'settings.reviews.schema.address' => 'settings.schema.address',
        'settings.reviews.schema.description.custom' => 'settings.schema.description.custom',
        'settings.reviews.schema.description.default' => 'settings.schema.description.default',
        'settings.reviews.schema.highprice' => 'settings.schema.highprice',
        'settings.reviews.schema.image.custom' => 'settings.schema.image.custom',
        'settings.reviews.schema.image.default' => 'settings.schema.image.default',
        'settings.reviews.schema.lowprice' => 'settings.schema.lowprice',
        'settings.reviews.schema.name.custom' => 'settings.schema.name.custom',
        'settings.reviews.schema.name.default' => 'settings.schema.name.default',
        'settings.reviews.schema.pricecurrency' => 'settings.schema.pricecurrency',
        'settings.reviews.schema.pricerange' => 'settings.schema.pricerange',
        'settings.reviews.schema.telephone' => 'settings.schema.telephone',
        'settings.reviews.schema.type.custom' => 'settings.schema.type.custom',
        'settings.reviews.schema.type.default' => 'settings.schema.type.default',
        'settings.reviews.schema.url.custom' => 'settings.schema.url.custom',
        'settings.reviews.schema.url.default' => 'settings.schema.url.default',
        'version' => 'version_upgraded_from',
    ];

    /**
     * @var array
     */
    protected $newSettings;

    /**
     * @var array
     */
    protected $oldSettings;

    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    protected function migrateSettings(): void
    {
        $this->newSettings = $this->getNewSettings();
        $this->oldSettings = $this->getOldSettings();
        if (empty($this->oldSettings) || empty($this->newSettings)) {
            return;
        }
        $this->mapSettings();
        $this->migrateNotificationSettings();
        $this->migrateRecaptchaSettings();
        $this->migrateRequiredSettings();
        $oldSettings = Arr::unflatten($this->oldSettings);
        $newSettings = Arr::unflatten($this->newSettings);
        if (isset($oldSettings['settings']['strings']) && is_array($oldSettings['settings']['strings'])) {
            $newSettings['settings']['strings'] = $oldSettings['settings']['strings'];
        }
        update_option(OptionManager::databaseKey(3), $newSettings, true);
    }

    protected function getNewSettings(): array
    {
        $settings = get_option(OptionManager::databaseKey(3));
        return Arr::flatten(Arr::consolidate($settings));
    }

    protected function getOldSettings(): array
    {
        $defaults = array_fill_keys(array_keys(static::MAPPED_SETTINGS), '');
        $settings = get_option(OptionManager::databaseKey(2));
        $settings = Arr::flatten(Arr::consolidate($settings));
        return !empty($settings)
            ? wp_parse_args($settings, $defaults)
            : [];
    }

    protected function mapSettings(): void
    {
        foreach (static::MAPPED_SETTINGS as $old => $new) {
            unset($this->newSettings[$old]);
            if (!empty($this->oldSettings[$old])) {
                $this->newSettings[$new] = $this->oldSettings[$old];
            }
        }
    }

    protected function migrateNotificationSettings(): void
    {
        $notifications = [
            'custom' => 'custom',
            'default' => 'admin',
            'webhook' => 'slack',
        ];
        $this->newSettings['settings.general.notifications'] = [];
        foreach ($notifications as $old => $new) {
            if ($this->oldSettings['settings.general.notification'] === $old) {
                $this->newSettings['settings.general.notifications'][] = $new;
            }
        }
    }

    protected function migrateRecaptchaSettings(): void
    {
        $recaptcha = [
            'BadgePosition' => $this->oldSettings['settings.reviews-form.recaptcha.position'],
            'SecretKey' => $this->oldSettings['settings.reviews-form.recaptcha.secret'],
            'SiteKey' => $this->oldSettings['settings.reviews-form.recaptcha.key'],
        ];
        if (in_array($this->oldSettings['settings.reviews-form.recaptcha.integration'], ['custom', 'invisible-recaptcha'])) {
            $this->newSettings['settings.submissions.recaptcha.integration'] = 'all';
        }
        if ('invisible-recaptcha' === $this->oldSettings['settings.reviews-form.recaptcha.integration']) {
            $recaptcha = wp_parse_args((array) get_site_option('ic-settings', []), $recaptcha);
        }
        $this->newSettings['settings.submissions.recaptcha.key'] = $recaptcha['SiteKey'];
        $this->newSettings['settings.submissions.recaptcha.secret'] = $recaptcha['SecretKey'];
        $this->newSettings['settings.submissions.recaptcha.position'] = $recaptcha['BadgePosition'];
    }

    protected function migrateRequiredSettings(): void
    {
        $this->newSettings['settings.submissions.required'] = array_filter((array) $this->oldSettings['settings.reviews-form.required']);
        $this->newSettings['settings.submissions.required'][] = 'rating';
        $this->newSettings['settings.submissions.required'][] = 'terms';
    }
}
