<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Exceptions\LicenseException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Notice;

class SettingsController extends Controller
{
    /**
     * @return void
     * @action admin_init
     */
    public function registerSettings()
    {
        register_setting(glsr()->id.'-settings', OptionManager::databaseKey(), [
            'sanitize_callback' => [$this, 'sanitizeSettings'],
        ]);
    }

    /**
     * @param mixed $input
     * @return array
     * @callback register_setting
     */
    public function sanitizeSettings($input)
    {
        $settings = Arr::consolidate($input);
        if (1 === count($settings) && array_key_exists('settings', $settings)) {
            $options = array_replace_recursive(glsr(OptionManager::class)->all(), $input);
            $options = $this->sanitizeGeneral($input, $options);
            $options = $this->sanitizeLicenses($input, $options);
            $options = $this->sanitizeForms($input, $options);
            $options = $this->sanitizeStrings($input, $options);
            $options = glsr()->filterArray('settings/sanitize', $options, $settings);
            glsr()->action('settings/updated', $options, $settings);
            if (filter_input(INPUT_POST, 'option_page') == glsr()->id.'-settings') {
                glsr(Notice::class)->addSuccess(_x('Settings updated.', 'admin-text', 'site-reviews'));
            }
            glsr(Notice::class)->store(); // store the notices before the page reloads
            return $options;
        }
        return $input;
    }

    /**
     * @return array
     */
    protected function sanitizeForms(array $input, array $options)
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

    /**
     * @return array
     */
    protected function sanitizeGeneral(array $input, array $options)
    {
        $key = 'settings.general';
        $inputForm = Arr::get($input, $key);
        if (!$this->hasMultilingualIntegration(Arr::get($inputForm, 'multilingual'))) {
            $options = Arr::set($options, $key.'.multilingual', '');
        }
        if ('' == trim(Arr::get($inputForm, 'notification_message'))) {
            $defaultValue = Arr::get(glsr()->defaults, $key.'.notification_message');
            $options = Arr::set($options, $key.'.notification_message', $defaultValue);
        }
        $defaultValue = Arr::get($inputForm, 'notifications', []);
        $options = Arr::set($options, $key.'.notifications', $defaultValue);
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeLicenses(array $input, array $options)
    {
        $key = 'settings.licenses';
        $licenses = Arr::consolidate(Arr::get($input, $key));
        foreach ($licenses as $slug => &$license) {
            if (empty($license)) {
                continue;
            }
            $license = $this->verifyLicense($license, $slug);
        }
        $options = Arr::set($options, $key, $licenses);
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeStrings(array $input, array $options)
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

    /**
     * @param string $integrationSlug
     * @return bool
     */
    protected function hasMultilingualIntegration($integrationSlug)
    {
        $integration = glsr(Multilingual::class)->getIntegration($integrationSlug);
        if (!$integration) {
            return false;
        }
        if (!$integration->isActive()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please install/activate the %s plugin to enable integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName
            ));
            return false;
        } elseif (!$integration->isSupported()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please update the %s plugin to v%s or greater to enable integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName,
                $integration->supportedVersion
            ));
            return false;
        }
        return true;
    }

    /**
     * @param string $license
     * @param string $addonId
     * @return string
     */
    protected function verifyLicense($license, $addonId)
    {
        if (empty(glsr()->updated[$addonId])) {
            glsr_log()->error('Unknown add-on: '.$addonId);
            glsr(Notice::class)->addError(_x('A license you entered could not be verified for the selected add-on.', 'admin-text', 'site-reviews'));
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
