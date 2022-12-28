<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\CountManager;

class RegisterPostMeta implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        $metaKeys = [
            CountManager::META_AVERAGE,
            CountManager::META_RANKING,
            CountManager::META_REVIEWS,
        ];
        foreach ($metaKeys as $key) {
            register_post_meta('', $key, [ // register on all post types
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
