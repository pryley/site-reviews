<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;

class VersionDetailsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'autoupdate' => 'bool',
        'banners' => 'array',
        'sections' => 'array',
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
        'author' => 'text-html:a',
        'download_link' => 'url',
        'homepage' => 'url',
        'last_updated' => 'date',
        'name' => 'text',
        'requires' => 'version',
        'requires_php' => 'version',
        'slug' => 'text',
        'tested' => 'version',
        'version' => 'version',
    ];

    protected function defaults(): array
    {
        return [
            'author' => '',
            'autoupdate' => true,
            'banners' => [],
            'download_link' => '',
            'homepage' => '',
            'last_updated' => '',
            'name' => '',
            'requires' => '',
            'requires_php' => '',
            'sections' => [],
            'slug' => '',
            'tested' => '',
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
        return $values;
    }
}
