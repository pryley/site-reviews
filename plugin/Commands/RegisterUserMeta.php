<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\ColumnFilterbyDefaults;

/**
 * The per-user preferences the admin saves through WordPress's users route.
 */
class RegisterUserMeta extends AbstractCommand
{
    public function handle(): void
    {
        register_meta('user', 'edit_'.glsr()->post_type.'_filters', [
            'default' => ['rating'],
            'description' => _x('The filters shown above the reviews table.', 'admin-text', 'site-reviews'),
            'show_in_rest' => [
                'schema' => [
                    'context' => ['edit'], // not on the public users API
                    'items' => [
                        'enum' => array_keys(glsr(ColumnFilterbyDefaults::class)->defaults()),
                        'type' => 'string',
                    ],
                    'type' => 'array',
                ],
            ],
            'single' => true,
            'type' => 'array',
        ]);
    }
}
