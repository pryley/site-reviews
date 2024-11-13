<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Exceptions\LicenseException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class SettingsController extends AbstractController
{
    /**
     * @action admin_init
     */
    public function registerSettings(): void
    {
        register_setting(glsr()->id, OptionManager::databaseKey(), [
            'default' => glsr()->defaults(),
            'sanitize_callback' => [$this, 'sanitizeSettingsCallback'],
            'type' => 'array',
        ]);
    }

    /**
     * @param mixed $input
     *
     * @see registerSettings
     */
    public function sanitizeSettingsCallback($input): array
    {
        OptionManager::flushSettingsCache(); // remove settings from object cache before updating
        $input = Arr::consolidate($input);
        if (!array_key_exists('settings', $input)) {
            return $input;
        }
        $options = array_replace_recursive(glsr(OptionManager::class)->all(), [
            'settings' => $input['settings'],
        ]);
        $options = $this->sanitizeForms($options, $input);
        $options = $this->sanitizeGeneral($options, $input);
        $options = $this->sanitizeStrings($options, $input);
        $options = $this->sanitizeAll($options);
        $options = glsr()->filterArray('settings/sanitize', $options, $input);
        glsr()->action('settings/updated', $options, $input);
        if (filter_input(INPUT_POST, 'option_page') === glsr()->id) {
            glsr(Notice::class)->addSuccess(_x('Settings updated.', 'admin-text', 'site-reviews'));
        }
        glsr(Notice::class)->store(); // store the notices before the page reloads
        return $options;
    }

    protected function sanitizeAll(array $options): array
    {
        $values = Arr::flatten($options);
        $sanitizers = wp_list_pluck(glsr()->settings(), 'sanitizer');
        $options = (new Sanitizer($values, $sanitizers))->run();
        return Arr::unflatten($options);
    }

    protected function sanitizeForms(array $options, array $input): array
    {
        $key = 'settings.forms';
        $inputForm = Arr::get($input, $key);
        $multiFields = ['limit_assignments', 'required'];
        foreach ($multiFields as $name) {
            $defaultValue = Arr::get($inputForm, $name, []);
            $options = Arr::set($options, "{$key}.{$name}", $defaultValue);
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
            $defaultValue = Arr::get(glsr()->defaults(), $key.'.notification_message');
            $options = Arr::set($options, $key.'.notification_message', $defaultValue);
        }
        if ('' === trim(Arr::get($inputForm, 'request_verification_message'))) {
            $defaultValue = Arr::get(glsr()->defaults(), $key.'.request_verification_message');
            $options = Arr::set($options, $key.'.request_verification_message', $defaultValue);
        }
        $defaultValue = Arr::get($inputForm, 'notifications', []);
        $options = Arr::set($options, $key.'.notifications', $defaultValue);
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
            $errors = [
                '%d' => [],
                '%s' => [],
            ];
            array_walk($options['settings']['strings'], function (&$string) use ($allowedTags, &$errors) {
                $string = wp_parse_args($string, [
                    'p1' => '',
                    'p2' => '',
                    's1' => '',
                    's2' => '',
                ]);
                $string['s2'] = wp_kses($string['s2'], $allowedTags);
                $string['p2'] = wp_kses($string['p2'], $allowedTags);
                foreach ($errors as $needle => $values) {
                    if (str_contains($string['s1'], $needle) && !str_contains($string['s2'], $needle)) {
                        $errors[$needle][] = $string['s2'];
                    }
                    if (str_contains($string['p1'], $needle) && !str_contains($string['p2'], $needle)) {
                        $errors[$needle][] = $string['p2'];
                    }
                }
            });
            foreach ($errors as $needle => $values) {
                if (!empty($errors[$needle])) {
                    $notice = sprintf(_x('You forgot to include the %s placeholder tags in your Custom Text.', 'admin-text', 'site-reviews'),
                        "<code>{$needle}</code>"
                    );
                    glsr(Notice::class)->addError($notice, $errors[$needle]);
                }
            }
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
}
