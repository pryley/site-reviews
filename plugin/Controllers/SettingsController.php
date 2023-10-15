<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Exceptions\LicenseException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class SettingsController extends Controller
{
    /**
     * @action admin_init
     */
    public function registerSettings(): void
    {
        register_setting(glsr()->id, OptionManager::databaseKey(), [
            'default' => glsr()->defaults,
            'sanitize_callback' => [$this, 'sanitizeSettings'],
            'type' => 'array',
        ]);
    }

    /**
     * @param mixed $input
     * @callback register_setting
     */
    public function sanitizeSettings($input): array
    {
        OptionManager::flushCache(); // remove settings from object cache before updating
        $settings = Arr::consolidate($input);
        if (1 === count($settings) && array_key_exists('settings', $settings)) {
            $options = array_replace_recursive(glsr(OptionManager::class)->all(), $input);
            $options = $this->sanitizeForms($options, $input);
            $options = $this->sanitizeGeneral($options, $input);
            $options = $this->sanitizeLicenses($options, $input);
            $options = $this->sanitizeStrings($options, $input);
            $options = $this->sanitizeAll($options);
            $options = glsr()->filterArray('settings/sanitize', $options, $settings);
            glsr()->action('settings/updated', $options, $settings);
            if (filter_input(INPUT_POST, 'option_page') === glsr()->id) {
                glsr(Notice::class)->addSuccess(_x('Settings updated.', 'admin-text', 'site-reviews'));
            }
            glsr(Notice::class)->store(); // store the notices before the page reloads
            return $options;
        }
        return $input;
    }

    protected function sanitizeAll(array $options): array
    {
        $values = Arr::flatten($options);
        $sanitizers = wp_list_pluck(glsr()->settings(), 'sanitizer');
        $options = (new Sanitizer($values, $sanitizers))->run();
        return Arr::convertFromDotNotation($options);
    }

    protected function sanitizeForms(array $options, array $input): array
    {
        $key = 'settings.forms';
        $inputForm = Arr::get($input, $key);
        $multiFields = ['limit_assignments', 'required'];
        foreach ($multiFields as $name) {
            $defaultValue = Arr::get($inputForm, $name, []);
            $options = Arr::set($options, $key.'.'.$name, $defaultValue);
        }
        return $options;
    }

    protected function sanitizeGeneral(array $options, array $input): array
    {
        $key = 'settings.general';
        $inputForm = Arr::get($input, $key);
        if (!$this->hasMultilingualIntegration(Arr::getAs('string', $inputForm, 'multilingual'))) {
            $options = Arr::set($options, $key.'.multilingual', '');
        }
        if ('' === trim(Arr::get($inputForm, 'notification_message'))) {
            $defaultValue = Arr::get(glsr()->defaults, $key.'.notification_message');
            $options = Arr::set($options, $key.'.notification_message', $defaultValue);
        }
        if ('' === trim(Arr::get($inputForm, 'request_verification_message'))) {
            $defaultValue = Arr::get(glsr()->defaults, $key.'.request_verification_message');
            $options = Arr::set($options, $key.'.request_verification_message', $defaultValue);
        }
        $defaultValue = Arr::get($inputForm, 'notifications', []);
        $options = Arr::set($options, $key.'.notifications', $defaultValue);
        return $options;
    }

    protected function sanitizeLicenses(array $options, array $input): array
    {
        $key = 'settings.licenses';
        $licenses = Arr::consolidate(Arr::get($input, $key));
        foreach ($licenses as $slug => &$license) {
            if (!empty($license)) {
                $license = $this->verifyLicense((string) $license, (string) $slug);
            }
        }
        $options = Arr::set($options, $key, $licenses);
        return $options;
    }

    protected function sanitizeStrings(array $options, array $input): array
    {
        $key = 'settings.strings';
        $inputForm = Arr::consolidate(Arr::get($input, $key));
        if (!empty($inputForm)) {
            $options = Arr::set($options, $key, array_values(array_filter($inputForm)));
            $allowedTags = [
                'a' => ['class' => [], 'href' => [], 'target' => []],
                'span' => ['class' => []],
            ];
            array_walk($options['settings']['strings'], function (&$string) use ($allowedTags) {
                if (isset($string['s2'])) {
                    $string['s2'] = wp_kses($string['s2'], $allowedTags);
                }
                if (isset($string['p2'])) {
                    $string['p2'] = wp_kses($string['p2'], $allowedTags);
                }
            });
        }
        return $options;
    }

    protected function hasMultilingualIntegration(string $option): bool
    {
        $integration = glsr(Multilingual::class)->getIntegration($option);
        if (!$integration) {
            return false;
        }
        if (!$integration->isActive()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please install/activate the %s plugin to enable the integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName
            ));
            return false;
        } elseif (!$integration->isSupported()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please update the %s plugin to v%s or greater to enable the integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName,
                $integration->supportedVersion
            ));
            return false;
        }
        return true;
    }

    protected function verifyLicense(string $license, string $addonId): string
    {
        if (empty(glsr()->updated[$addonId])) {
            glsr_log()->error('Unknown addon: '.$addonId);
            glsr(Notice::class)->addError(_x('A license you entered could not be verified for the selected addon.', 'admin-text', 'site-reviews'));
            return '';
        }
        try {
            $addon = glsr()->updated[$addonId];
            $updater = new Updater($addon['updateUrl'], $addon['file'], $addonId, compact('license'));
            if (!$updater->isLicenseValid()) {
                throw new LicenseException('Invalid license: '.$license.' ('.$addonId.')');
            }
        } catch (LicenseException $e) {
            $license = '';
            glsr_log()->error($e->getMessage());
            $error = _x('A license you entered is either invalid or has not yet been authorized for your website.', 'admin-text', 'site-reviews');
            $message = sprintf(_x('To activate your license, please visit the %s page on your Nifty Plugins account and click the "Manage Sites" button to activate it for your website.', 'admin-text', 'site-reviews'),
                sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
            );
            glsr(Notice::class)->addError(sprintf('<strong>%s</strong><br>%s', $error, $message));
        }
        return $license;
    }
}
