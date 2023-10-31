<?php

namespace GeminiLabs\SiteReviews\Defaults;

class PermissionDefaults extends DefaultsAbstract
{
    protected function defaults(): array
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
            'notices' => [
                'footer' => 'edit_posts',
                'migration' => 'edit_others_posts',
                'premium' => 'edit_others_posts',
                'upgraded' => 'update_plugins',
                'welcome' => 'edit_posts',
                'write-review' => 'edit_posts',
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
                'index' => 'edit_others_posts',
                'scheduled' => 'edit_others_posts',
                'sync' => 'edit_others_posts',
                'system-info' => 'edit_others_posts',
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
