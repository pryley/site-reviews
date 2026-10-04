<?php

namespace GeminiLabs\SiteReviews\Compat;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * Runs the two filters that received the localized variables before 8.4.0,
 * site-reviews/enqueue/{admin,public}/localize
 */
class LocalizeFilters
{
    /**
     * The name of a key in the 8.x array => its name in the config.
     */
    protected const KEYS = [
        'captcha' => [
            'captcha_type' => 'captchaType',
            'token_field' => 'tokenField',
        ],
        'text' => [
            'close_modal' => 'closeModal',
            'import_error' => 'importError',
            'rollback_error' => 'rollbackError',
            'system_info_500' => 'systemInfo500',
            'system_info_error' => 'systemInfoError',
            'system_info_failed' => 'systemInfoFailed',
        ],
        'validation_config' => [
            'field_error' => 'fieldError',
            'field_hidden' => 'fieldHidden',
            'field_message' => 'fieldMessage',
            'field_required' => 'fieldRequired',
            'field_valid' => 'fieldValid',
            'form_error' => 'formError',
            'form_message' => 'formMessage',
            'form_message_failed' => 'formMessageFailed',
            'form_message_success' => 'formMessageSuccess',
            'input_error' => 'inputError',
            'input_valid' => 'inputValid',
        ],
    ];

    /**
     * A key of the 8.x array => its path in the config.
     */
    protected const PATHS = [
        'admin' => [
            'action' => 'request.ajax.action',
            'addonsurl' => 'urls.addons',
            'maxrating' => 'rating.max',
            'minrating' => 'rating.min',
            'nameprefix' => 'nameprefix',
            'nonce' => 'nonce',
            'shortcodes' => 'tinymce.required',
            'text' => 'text',
            'tinymce' => 'tinymce.plugins',
        ],
        'public' => [
            'action' => 'request.ajax.action',
            'ajax_pagination' => 'pagination.fixed',
            'ajax_url' => 'request.ajax.url',
            'captcha' => 'captcha',
            'modal_wrapped_by' => 'modal.wrappedBy',
            'nameprefix' => 'nameprefix',
            'rest_nonce' => 'request.nonce',
            'rest_url' => 'request.url',
            'stars_config' => 'rating',
            'text' => 'text',
            'url_parameter' => 'pagination.urlParameter',
            'validation_config' => 'validation',
            'validation_strings' => 'validation.strings',
        ],
    ];

    /**
     * @return array{0: array, 1: array} The config, and the keys that the filters added
     */
    public function apply(string $bundle, array $config): array
    {
        $hook = "enqueue/{$bundle}/localize";
        if (!has_filter(glsr()->hookPrefix().'/'.$hook)) {
            return [$config, []];
        }
        $added = glsr()->filterArray($hook, $this->variables($bundle, $config));
        foreach (static::PATHS[$bundle] as $key => $path) {
            if (array_key_exists($key, $added)) {
                $config = Arr::set($config, $path, $this->configValue($key, $added[$key], $config));
            }
            unset($added[$key]);
        }
        // What is left is printed as GLSR.{key}, which must not replace a key that 8.4.0 writes.
        unset($added['Event'], $added['config'], $added['state'], $added['version']);
        return [$config, $added];
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function configValue(string $key, $value, array $config)
    {
        $value = $this->rename($value, static::KEYS[$key] ?? []);
        if ('validation_config' === $key && is_array($value)) {
            $value['strings'] = Arr::get($config, 'validation.strings', []);
        }
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function rename($value, array $names)
    {
        if (empty($names) || !is_array($value)) {
            return $value;
        }
        $renamed = [];
        foreach ($value as $name => $item) {
            $renamed[$names[$name] ?? $name] = $item;
        }
        return $renamed;
    }

    protected function variables(string $bundle, array $config): array
    {
        $variables = ['addons' => []];
        foreach (static::PATHS[$bundle] as $key => $path) {
            $value = Arr::get($config, $path, null);
            if (null === $value) {
                continue; // a subject that a later site-reviews/assets/config callback writes
            }
            if ('validation_config' === $key && is_array($value)) {
                unset($value['strings']);
            }
            $variables[$key] = $this->rename($value, array_flip(static::KEYS[$key] ?? []));
        }
        if ('public' === $bundle) {
            $variables['state'] = ['popstate' => false];
            $variables['version'] = glsr()->version;
        }
        ksort($variables);
        return $variables;
    }
}
