<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\CountManager;

class RegisterPostMeta extends AbstractCommand
{
    public function handle(): void
    {
        $metaKeys = [
            CountManager::META_AVERAGE,
            CountManager::META_RANKING,
            CountManager::META_REVIEWS,
        ];
        $types = array_keys(get_post_types([
            '_builtin' => false,
            'exclude_from_search' => false,
            'show_in_rest' => true,
        ]));
        $types[] = 'page';
        $types[] = 'post';
        foreach ($metaKeys as $key) {
            foreach ($types as $type) {
                register_post_meta($type, $key, [
                    'auth_callback' => '__return_true',
                    'default' => 0,
                    'sanitize_callback' => 'sanitize_text_field',
                    'show_in_rest' => true,
                    'single' => true,
                    'type' => 'number',
                ]);
            }
        }
    }
}
