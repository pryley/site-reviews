<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Defaults\PointerDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;
use GeminiLabs\SiteReviews\Modules\Assets\InlineScript;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\RequestConfig;
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
            'rating' => [
                'max' => Rating::max(),
                'min' => Rating::min(),
            ],
            'request' => glsr(RequestConfig::class)->toArray($this->routes()),
            'text' => [
                'cancel' => _x('Cancel', 'admin-text', 'site-reviews'),
                'cancelling' => _x('Cancelling, please wait...', 'admin-text', 'site-reviews'),
                /* translators: %s: maximum file upload size */
                'importError' => sprintf(_x('Your server restricts file uploads to less than %s in size.', 'admin-text', 'site-reviews'),
                    (string) size_format(wp_max_upload_size())
                ),
                'nothingFound' => _x('Nothing found.', 'admin-text', 'site-reviews'),
                'premiumConnecting' => _x('Just a moment while we connect your site and install Site Reviews Premium.', 'admin-text', 'site-reviews'),
                'premiumError' => _x('Something went wrong. Please try again.', 'admin-text', 'site-reviews'),
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
            glsr()->url('assets/scripts/admin/admin.js'),
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
        wp_add_inline_script($last, glsr()->filterString('enqueue/admin/inline-script/after', ''));
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style('wp-color-picker');
        wp_register_style(
            glsr()->id.'/admin',
            glsr()->url('assets/styles/admin/admin.css'),
            ['wp-components'], // styles the components-button classes
            glsr()->version
        );
        wp_enqueue_style(glsr()->id.'/admin');
        wp_add_inline_style(glsr()->id.'/admin', $this->inlineStyles());
    }

    public function enqueueWidgetStyles(): void
    {
        wp_enqueue_style(glsr()->id.'/widgets', glsr()->url('assets/styles/admin/widgets.css'), [], glsr()->version);
    }

    public function handle(): void
    {
        if ($this->isWidgetsScreen()) {
            $this->enqueueWidgetStyles();
        }
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
            'jquery', 'jquery-ui-sortable', 'wp-color-picker',
        ]);
        return $dependencies;
    }

    protected function isCurrentScreen(): bool
    {
        $screen = glsr_current_screen();
        if ('admin' === $screen->base && str_starts_with((string) filter_input(\INPUT_GET, 'import'), glsr()->post_type)) {
            return true;
        }
        $isCurrentScreen = str_starts_with($screen->post_type, glsr()->post_type)
            || 'dashboard_page_'.glsr()->id.'-welcome' === $screen->id;
        return glsr()->filterBool('enqueue/admin/screen', $isCurrentScreen, $screen);
    }

    protected function isWidgetsScreen(): bool
    {
        return in_array(glsr_current_screen()->id, ['customize', 'site-editor', 'widgets']);
    }

    /**
     * The route names sent by the admin script with method and path template.
     *
     * @return array<string, array{method: string, path: string}>
     */
    protected function routes(): array
    {
        return [
            'console' => ['method' => 'GET', 'path' => 'plugin/console'],
            'console-clear' => ['method' => 'DELETE', 'path' => 'plugin/console'],
            'console-level' => ['method' => 'PUT', 'path' => 'plugin/console/level'],
            'counts' => ['method' => 'POST', 'path' => 'reviews/counts'],
            'import' => ['method' => 'POST', 'path' => 'reviews/import'],
            'ip-address' => ['method' => 'GET', 'path' => 'plugin/ip-address'],
            'ip-address-proxy' => ['method' => 'PUT', 'path' => 'plugin/ip-address/proxy'],
            'locations' => ['method' => 'POST', 'path' => 'reviews/locations'],
            'locations-remove' => ['method' => 'DELETE', 'path' => 'reviews/locations'],
            'migrations' => ['method' => 'POST', 'path' => 'plugin/migrations'],
            'permissions' => ['method' => 'PUT', 'path' => 'plugin/permissions'],
            'relations-remove' => ['method' => 'DELETE', 'path' => 'reviews/relations'],
            'rollback' => ['method' => 'POST', 'path' => 'plugin/rollback'],
            'system-info' => ['method' => 'GET', 'path' => 'plugin/system-info'],
            'table-engine' => ['method' => 'PUT', 'path' => 'plugin/tables/{table}/engine'],
        ];
    }
}
