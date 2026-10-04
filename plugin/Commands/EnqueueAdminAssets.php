<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\PointerDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;
use GeminiLabs\SiteReviews\Modules\Assets\InlineScript;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class EnqueueAdminAssets extends AbstractCommand
{
    public function config(): array
    {
        return [
            'nameprefix' => glsr()->id,
            'nonce' => [
                'clear-console' => wp_create_nonce('clear-console'),
                'console-level' => wp_create_nonce('console-level'),
                'fetch-console' => wp_create_nonce('fetch-console'),
                'geolocate-reviews' => wp_create_nonce('geolocate-reviews'),
                'mce-shortcode' => wp_create_nonce('mce-shortcode'),
                'search-posts' => wp_create_nonce('search-posts'),
                'search-strings' => wp_create_nonce('search-strings'),
                'search-users' => wp_create_nonce('search-users'),
                'sync-reviews' => wp_create_nonce('sync-reviews'),
                'system-info' => wp_create_nonce('system-info'),
                'toggle-filters' => wp_create_nonce('toggle-filters'),
                'toggle-pinned' => wp_create_nonce('toggle-pinned'),
                'toggle-status' => wp_create_nonce('toggle-status'),
                'toggle-verified' => wp_create_nonce('toggle-verified'),
            ],
            'rating' => [
                'max' => Rating::max(),
                'min' => Rating::min(),
            ],
            'request' => [
                'ajax' => [
                    'action' => glsr()->prefix.'admin_action',
                    'rest' => glsr()->prefix.'rest_request',
                    'url' => admin_url('admin-ajax.php'),
                ],
                'nonce' => wp_create_nonce('wp_rest'),
                'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
            ],
            'text' => [
                'cancel' => _x('Cancel', 'admin-text', 'site-reviews'),
                'cancelling' => _x('Cancelling, please wait...', 'admin-text', 'site-reviews'),
                /* translators: %s: maximum file upload size */
                'importError' => sprintf(_x('Your server restricts file uploads to less than %s in size.', 'admin-text', 'site-reviews'),
                    (string) size_format(wp_max_upload_size())
                ),
                'rollbackError' => _x('Rollback failed', 'admin-text', 'site-reviews'),
                'searching' => _x('Searching...', 'admin-text', 'site-reviews'),
                /* translators: %s: Site Health Info page URL */
                'systemInfo500' => sprintf(_x('Site Reviews was unable to fetch the System Info because WordPress crashed when getting the <a href="%s">Site Health Info</a>.', 'admin-text', 'site-reviews'),
                    admin_url('site-health.php?tab=debug')
                ),
                /* translators: %1$s: HTTP response status code, %2$s: response error message */
                'systemInfoError' => _x('Site Reviews was unable to fetch the System Info because your server threw an error: %1$s %2$s', 'admin-text', 'site-reviews'),
                'systemInfoFailed' => _x('Unable to fetch the System Info.', 'admin-text', 'site-reviews'),
            ],
            'urls' => [
                'addons' => glsr_admin_url('addons'),
            ],
        ];
    }

    public function enqueueScripts(): void
    {
        wp_register_script(
            glsr()->id.'/admin',
            glsr()->url('assets/scripts/'.glsr()->id.'-admin.js'),
            $this->getDependencies(),
            glsr()->version,
            ['strategy' => 'defer']
        );
        wp_enqueue_script(glsr()->id.'/admin');
        wp_add_inline_script(glsr()->id.'/admin', $this->inlineScript(), 'before');
        $compat = glsr(CompatScript::class);
        $last = glsr()->id.'/admin';
        if ($compat->isEnabled('admin')) {
            $last = $compat->scriptHandle('admin');
            wp_enqueue_script($last, $compat->url('admin'), [glsr()->id.'/admin'], glsr()->version, ['strategy' => 'defer']);
        }
        // a script added with this filter can read a deprecated key as it is parsed
        wp_add_inline_script($last, glsr()->filterString('enqueue/admin/inline-script/after', ''));
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style('wp-color-picker');
        wp_register_style(
            glsr()->id.'/admin',
            glsr()->url('assets/styles/admin/admin.css'),
            ['wp-list-reusable-blocks'], // loads the :root admin theme colors
            glsr()->version
        );
        wp_enqueue_style(glsr()->id.'/admin');
        wp_add_inline_style(glsr()->id.'/admin', $this->inlineStyles());
    }

    public function handle(): void
    {
        if (!$this->isCurrentScreen()) {
            $this->fail();
            return;
        }
        $this->enqueueStyles();
        $this->enqueueScripts();
    }

    public function inlineScript(): string
    {
        return glsr(InlineScript::class)->build('admin', $this->config());
    }

    public function inlineStyles(): string
    {
        return glsr()->filterString('enqueue/admin/inline-styles', '');
    }

    protected function getDependencies(): array
    {
        $dependencies = glsr()->filterArray('enqueue/admin/dependencies', []);
        $dependencies = array_merge($dependencies, [
            'jquery', 'jquery-ui-sortable', 'underscore', 'wp-color-picker', 'wp-util',
        ]);
        return $dependencies;
    }

    protected function isCurrentScreen(): bool
    {
        if (is_customize_preview()) {
            return false; // don't load assets in the Customizer preview
        }
        $screen = glsr_current_screen();
        $screenIds = [
            'customize',
            'dashboard',
            'dashboard_page_'.glsr()->id.'-welcome',
            'plugins_page_'.glsr()->id,
            'site-editor',
            'widgets',
        ];
        if ('admin' === $screen->base && str_starts_with((string) filter_input(\INPUT_GET, 'import'), glsr()->post_type)) {
            return true;
        }
        $isCurrentScreen = str_starts_with($screen->post_type, glsr()->post_type)
            || in_array($screen->id, $screenIds)
            || 'post' === $screen->base;
        // A screen that provides its own editor app (the premium form
        // editor) opts out of the admin bundle here.
        return glsr()->filterBool('enqueue/admin/screen', $isCurrentScreen, $screen);
    }
}
