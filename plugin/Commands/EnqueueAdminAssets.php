<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedPost;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedUser;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAuthor;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;
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
    }

    /**
     * @return void
     */
    public function enqueueAssets()
    {
        if (!$this->isCurrentScreen()) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
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
        wp_add_inline_script(glsr()->id.'/admin', $this->inlineScript(), 'before');
        wp_add_inline_script(glsr()->id.'/admin', glsr()->filterString('enqueue/admin/inline-script/after', ''));
    }

    /**
     * @return string
     */
    public function inlineScript()
    {
        $variables = [
            'action' => glsr()->prefix.'action',
            'addons' => [],
            'addonsurl' => glsr_admin_url('addons'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'displayoptions' => [
                'site_reviews' => glsr(SiteReviewsShortcode::class)->getDisplayOptions(),
                'site_reviews_form' => glsr(SiteReviewsFormShortcode::class)->getDisplayOptions(),
                'site_reviews_summary' => glsr(SiteReviewsSummaryShortcode::class)->getDisplayOptions(),
            ],
            'filters' => [
                'assigned_post' => glsr(ColumnFilterAssignedPost::class)->options(),
                'assigned_user' => glsr(ColumnFilterAssignedUser::class)->options(),
                'author' => glsr(ColumnFilterAuthor::class)->options(),
                'post_author' => (object) [
                    '' => sprintf('&mdash; %s &mdash;', _x('No Change', 'admin-text', 'site-reviews')),
                    0 => _x('No Author', 'admin-text', 'site-reviews'),
                ],
                'post_author_override' => (object) [
                    0 => _x('Author Unknown', 'admin-text', 'site-reviews'),
                ],
            ],
            'hideoptions' => [
                'site_review' => glsr(SiteReviewShortcode::class)->getHideOptions(),
                'site_reviews' => glsr(SiteReviewsShortcode::class)->getHideOptions(),
                'site_reviews_form' => glsr(SiteReviewsFormShortcode::class)->getHideOptions(),
                'site_reviews_summary' => glsr(SiteReviewsSummaryShortcode::class)->getHideOptions(),
            ],
            'maxrating' => glsr()->constant('MAX_RATING', Rating::class),
            'minrating' => glsr()->constant('MIN_RATING', Rating::class),
            'nameprefix' => glsr()->id,
            'nonce' => [
                'clear-console' => wp_create_nonce('clear-console'),
                'console-level' => wp_create_nonce('console-level'),
                'fetch-console' => wp_create_nonce('fetch-console'),
                'filter-assigned_post' => wp_create_nonce('filter-assigned_post'),
                'filter-assigned_user' => wp_create_nonce('filter-assigned_user'),
                'filter-author' => wp_create_nonce('filter-author'),
                'mce-shortcode' => wp_create_nonce('mce-shortcode'),
                'search-posts' => wp_create_nonce('search-posts'),
                'search-strings' => wp_create_nonce('search-strings'),
                'search-users' => wp_create_nonce('search-users'),
                'sync-reviews' => wp_create_nonce('sync-reviews'),
                'toggle-filters' => wp_create_nonce('toggle-filters'),
                'toggle-pinned' => wp_create_nonce('toggle-pinned'),
                'toggle-status' => wp_create_nonce('toggle-status'),
                'toggle-verified' => wp_create_nonce('toggle-verified'),
            ],
            'pointers' => $this->pointers,
            'shortcodes' => [],
            'text' => [
                'rollback_error' => _x('Rollback failed', 'admin-text', 'site-reviews'),
                'searching' => _x('Searching...', 'admin-text', 'site-reviews'),
            ],
            'tinymce' => [
                'glsr_shortcode' => glsr()->url('assets/scripts/mce-plugin.js'),
            ],
        ];
        if (user_can_richedit()) {
            $variables['shortcodes'] = $this->localizeShortcodes();
        }
        $variables = glsr()->filterArray('enqueue/admin/localize', $variables);
        return $this->buildInlineScript($variables);
    }

    /**
     * @return string
     */
    protected function buildInlineScript(array $variables)
    {
        $script = 'window.hasOwnProperty("GLSR")||(window.GLSR={});';
        foreach ($variables as $key => $value) {
            $script .= sprintf('GLSR.%s=%s;', $key, json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $pattern = '/\"([a-zA-Z]+)\"(:[{\[\"])/'; // remove unnecessary quotes surrounding object keys
        $optimizedScript = preg_replace($pattern, '$1$2', $script);
        return glsr()->filterString('enqueue/admin/inline-script', $optimizedScript, $script, $variables);
    }

    /**
     * @return array
     */
    protected function getDependencies()
    {
        $dependencies = glsr()->filterArray('enqueue/admin/dependencies', []);
        $dependencies = array_merge($dependencies, [
            'jquery', 'jquery-ui-sortable', 'underscore', 'wp-color-picker', 'wp-util',
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
            'customize',
            'dashboard',
            'dashboard_page_'.glsr()->id.'-welcome',
            'plugins_page_'.glsr()->id,
            'site-editor',
            'widgets',
        ];
        return Str::startsWith($screen->post_type, glsr()->post_type)
            || in_array($screen->id, $screenIds)
            || 'post' === $screen->base;
    }

    /**
     * @return array
     */
    protected function localizeShortcodes()
    {
        $variables = [];
        foreach (glsr()->retrieveAs('array', 'mce', []) as $tag => $args) {
            if (empty($args['required'])) {
                continue;
            }
            $variables[$tag] = $args['required'];
        }
        return $variables;
    }
}
