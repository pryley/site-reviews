<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;

class VersionUpdateDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'banners' => 'array',
        'icons' => 'array',
        'translations' => 'array',
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
        'id' => 'slug',
        'package' => 'url',
        'requires' => 'version',
        'requires_php' => 'version',
        'slug' => 'slug',
        'tested' => 'version',
        'upgrade_notice' => 'text',
        'url' => 'url',
        'version' => 'version',
    ];

    protected function defaults(): array
    {
        return [
            'banners' => '',
            'icons' => '',
            'id' => '',
            'package' => '',
            'requires' => '',
            'requires_php' => '',
            'slug' => '',
            'tested' => '',
            'translations' => '',
            'upgrade_notice' => '',
            'url' => '',
            'version' => '',
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
        if (empty($values['package']) && empty($values['upgrade_notice'])) {
            $notice = _x('A valid license key is required to update this plugin.', 'admin-text', 'site-reviews');
            $values['upgrade_notice'] = sprintf('‼️ %s', $notice);
        }
        return $values;
    }
}
