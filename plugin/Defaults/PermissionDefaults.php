<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PermissionDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'addons' => [
                'index' => 'edit_posts',
            ],
            'documentation' => [
                'addons' => 'edit_posts',
                'api' => 'edit_others_posts',
                'faq' => 'edit_posts',
                'functions' => 'edit_others_posts',
                'hooks' => 'edit_others_posts',
                'index' => 'edit_posts',
                'shortcodes' => 'edit_posts',
                'support' => 'edit_others_posts',
            ],
            'settings' => [
                'addons' => 'manage_options',
                'forms' => 'manage_options',
                'general' => 'manage_options',
                'index' => 'manage_options',
                'licenses' => 'manage_options',
                'reviews' => 'manage_options',
                'schema' => 'manage_options',
                'strings' => 'manage_options',
            ],
            'tools' => [
                'console' => 'edit_others_posts',
                'general' => 'edit_others_posts',
                'index' => 'edit_posts',
                'scheduled' => 'edit_others_posts',
                'sync' => 'edit_others_posts',
                'system-info' => 'edit_posts',
            ],
            'welcome' => [
                'getting-started' => 'edit_posts',
                'index' => 'edit_posts',
                'support' => 'edit_posts',
                'upgrade-guide' => 'edit_posts',
                'whatsnew' => 'edit_posts',
            ],
        ];
    }
}
