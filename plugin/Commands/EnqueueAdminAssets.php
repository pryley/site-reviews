<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class EnqueueAdminAssets implements Contract
{
    public $pointers;

    public function __construct()
    {
        $this->pointers = $this->generatePointers([[
            'content' => _x('You can pin exceptional reviews so that they are always shown first.', 'admin-text', 'site-reviews'),
            'id' => 'glsr-pointer-pinned',
            'position' => [
                'edge' => 'right',
                'align' => 'middle',
            ],
            'screen' => glsr()->post_type,
            'target' => '#misc-pub-pinned',
            'title' => _x('Pin Your Reviews', 'admin-text', 'site-reviews'),
        ]]);
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->enqueueAssets();
        $this->localizeAssets();
    }

    /**
     * @return void
     */
    public function enqueueAssets()
    {
        if (!$this->isCurrentScreen()) {
            return;
        }
        wp_enqueue_style(
            glsr()->id.'/admin',
            glsr()->url('assets/styles/admin/admin.css'),
            ['wp-list-reusable-blocks'], // load the :root admin theme colors
            glsr()->version
        );
        wp_enqueue_script(
            glsr()->id.'/admin',
            glsr()->url('assets/scripts/'.glsr()->id.'-admin.js'),
            $this->getDependencies(),
            glsr()->version,
            false
        );
        if (!empty($this->pointers)) {
            wp_enqueue_style('wp-pointer');
            wp_enqueue_script('wp-pointer');
        }
    }

    /**
     * @return void
     */
    public function localizeAssets()
    {
        $variables = [
            'action' => glsr()->prefix.'action',
            'addons' => [],
            'ajaxurl' => admin_url('admin-ajax.php'),
            'hideoptions' => [
                'site_reviews' => glsr(SiteReviewsShortcode::class)->getHideOptions(),
                'site_reviews_form' => glsr(SiteReviewsFormShortcode::class)->getHideOptions(),
                'site_reviews_summary' => glsr(SiteReviewsSummaryShortcode::class)->getHideOptions(),
            ],
            'maxrating' => glsr()->constant('MAX_RATING', Rating::class),
            'minrating' => glsr()->constant('MIN_RATING', Rating::class),
            'nameprefix' => glsr()->id,
            'nonce' => [
                'clear-console' => wp_create_nonce('clear-console'),
                'fetch-console' => wp_create_nonce('fetch-console'),
                'mce-shortcode' => wp_create_nonce('mce-shortcode'),
                'sync-reviews' => wp_create_nonce('sync-reviews'),
                'toggle-pinned' => wp_create_nonce('toggle-pinned'),
                'toggle-status' => wp_create_nonce('toggle-status'),
            ],
            'pointers' => $this->pointers,
            'shortcodes' => [],
            'tinymce' => [
                'glsr_shortcode' => glsr()->url('assets/scripts/mce-plugin.js'),
            ],
        ];
        if (user_can_richedit()) {
            $variables['shortcodes'] = $this->localizeShortcodes();
        }
        $variables = glsr()->filterArray('enqueue/admin/localize', $variables);
        wp_localize_script(glsr()->id.'/admin', 'GLSR', $variables);
    }

    /**
     * @return array
     */
    protected function getDependencies()
    {
        $dependencies = glsr()->filterArray('enqueue/admin/dependencies', []);
        $dependencies = array_merge($dependencies, [
            'jquery', 'jquery-ui-sortable', 'underscore', 'wp-util',
        ]);
        return $dependencies;
    }

    /**
     * @return array
     */
    protected function generatePointer(array $pointer)
    {
        return [
            'id' => $pointer['id'],
            'options' => [
                'content' => '<h3>'.$pointer['title'].'</h3><p>'.$pointer['content'].'</p>',
                'position' => $pointer['position'],
            ],
            'screen' => $pointer['screen'],
            'target' => $pointer['target'],
        ];
    }

    /**
     * @return array
     */
    protected function generatePointers(array $pointers)
    {
        $dismissedPointers = get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true);
        $dismissedPointers = explode(',', (string) $dismissedPointers);
        $generatedPointers = [];
        foreach ($pointers as $pointer) {
            if ($pointer['screen'] != glsr_current_screen()->id) {
                continue;
            }
            if (in_array($pointer['id'], $dismissedPointers)) {
                continue;
            }
            $generatedPointers[] = $this->generatePointer($pointer);
        }
        return $generatedPointers;
    }

    /**
     * @return bool
     */
    protected function isCurrentScreen()
    {
        $screen = glsr_current_screen();
        $screenIds = [
            'dashboard',
            'dashboard_page_'.glsr()->id.'-welcome',
            'plugins_page_'.glsr()->id,
            'widgets',
        ];
        return Str::startsWith(glsr()->post_type, $screen->post_type)
            || in_array($screen->id, $screenIds)
            || 'post' == $screen->base;
    }

    /**
     * @return array
     */
    protected function localizeShortcodes()
    {
        $variables = [];
        foreach (glsr()->retrieve('mce', []) as $tag => $args) {
            if (empty($args['required'])) {
                continue;
            }
            $variables[$tag] = $args['required'];
        }
        return $variables;
    }
}
