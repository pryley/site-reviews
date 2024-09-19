<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;

class VersionDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'banners' => 'array',
        'contributors' => 'array',
        'faq' => 'array',
        'icons' => 'array',
        'screenshots' => 'array',
        'sections' => 'array',
        'tags' => 'array',
        'translations' => 'array',
        'warnings' => 'array',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'new_version' => 'version',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'donate_link' => 'url',
        'download_link' => 'url',
        'homepage' => 'url',
        'last_updated' => 'date',
        'license' => 'text',
        'msg' => 'text',
        'name' => 'text',
        'new_version' => 'version',
        'package' => 'url',
        'requires' => 'version',
        'requires_php' => 'version',
        'short_description' => 'text',
        'slug' => 'slug',
        'stable_tag' => 'version',
        'stable_version' => 'version',
        'tested' => 'version',
        'upgrade_notice' => 'text-html',
        'url' => 'url',
        'version' => 'version',
    ];

    protected function defaults(): array
    {
        return [
            'banners' => [],
            'contributors' => [],
            'donate_link' => '',
            'download_link' => '',
            'faq' => [],
            'homepage' => '',
            'icons' => [],
            'last_updated' => '',
            'license' => '',
            'msg' => '',
            'name' => '',
            'new_version' => '',
            'package' => '',
            'requires' => '',
            'requires_php' => '',
            'screenshots' => [],
            'sections' => [],
            'short_description' => '',
            'slug' => '',
            'stable_tag' => '',
            'stable_version' => '',
            'tags' => [],
            'tested' => '',
            'translations' => [],
            'upgrade_notice' => '',
            'url' => '',
            'warnings' => [],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        global $wp_version;
        if (empty($wp_version)) {
            $wp_version = get_bloginfo('version');
        }
        $tested = Helper::version($values['tested'], 'minor');
        $wp = Helper::version($wp_version, 'minor');
        if (version_compare($tested, $wp, '=')) {
            $values['tested'] = $wp_version;
        }
        return $values;
    }
}
