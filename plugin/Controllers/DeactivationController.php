<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\DeactivatePlugin;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;

class DeactivationController extends Controller
{
    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAssets(): void
    {
        global $pagenow;
        if ('plugins.php' !== $pagenow) {
            return;
        }
        wp_enqueue_style(
            glsr()->id.'/deactivate-plugin',
            glsr()->url('assets/styles/deactivate-plugin.css'),
            ['wp-list-reusable-blocks'], // load the :root admin theme colors
            glsr()->version
        );
        wp_enqueue_script(
            glsr()->id.'/deactivate-plugin',
            glsr()->url('assets/scripts/deactivate-plugin.js'),
            ['backbone', 'underscore'],
            glsr()->version,
            true
        );
        wp_localize_script(glsr()->id.'/deactivate-plugin', '_glsr_deactivate', [
            'ajax' => [
                'action' => glsr()->prefix.'action',
                'nonce' => wp_create_nonce('deactivate'),
                'prefix' => glsr()->id,
            ],
            'l10n' => [
                'buttonDeactivate' => _x('Skip & Deactivate', 'admin-text', 'site-reviews'),
                'buttonSubmit' => _x('Submit & Deactivate', 'admin-text', 'site-reviews'),
                'clickHere' => _x('Click here', 'admin-text', 'site-reviews'),
                'closeDialog' => _x('Close deactivation dialog', 'admin-text', 'site-reviews'),
                'dialogText' => _x('If you have a moment, please tell me how we can improve.', 'admin-text', 'site-reviews'),
                'dialogTextExtra' => _x('%s to see the additional data that will be submitted. This non-sensitive data will help me troubleshoot problems and make improvements to the plugin.', 'Click here (admin-text)', 'site-reviews'),
                'dialogTitle' => _x('Deactivating %s', 'Plugin name (admin-text)', 'site-reviews'),
                'overlayLabel' => _x('Deactivation Details', 'admin-text', 'site-reviews'),
                'processing' => _x('Processing...', 'admin-text', 'site-reviews'),
            ],
            'insight' => $this->deactivateInsight(),
            'plugins' => $this->deactivatePlugins(),
            'reasons' => $this->deactivateReasons(),
        ]);
    }

    /**
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links): array
    {
        if (array_key_exists('deactivate', $links)) {
            $replace = sprintf('<a data-deactivate="%s"', glsr()->id);
            $links['deactivate'] = str_replace('<a', $replace, $links['deactivate']);
        }
        return $links;
    }

    /**
     * @action admin_footer
     */
    public function renderTemplate(): void
    {
        global $pagenow;
        if ('plugins.php' === $pagenow) {
            glsr()->render('tmpl-deactivate-plugin');
        }
    }

    /**
     * @action site-reviews/route/ajax/deactivate
     */
    public function submitDeactivateReasonAjax(Request $request): void
    {
        $this->execute(new DeactivatePlugin($request));
        wp_send_json_success();
    }

    protected function deactivatePlugins(): array
    {
        $plugins = [
            [
                'name' => glsr()->name,
                'slug' => glsr()->id,
                'version' => glsr()->version,
            ],
        ];
        return glsr()->filterArray('deactivate/plugins', $plugins);
    }

    protected function deactivateReasons(): array
    {
        $reasons = [
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/confused.svg')),
                'id' => 'confused',
                'placeholder' => _x("Would like me to help you? If you enter your email I'll be happy to assist you.", 'admin-text', 'site-reviews'),
                'text' => _x("It's too complicated", 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/found-better.svg')),
                'id' => 'found-better',
                'placeholder' => _x('Which plugin?', 'admin-text', 'site-reviews'),
                'text' => _x('I found a better plugin', 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/not-working.svg')),
                'id' => 'not-working',
                'placeholder' => _x("What isn't working? Please let me know so I can fix it.", 'admin-text', 'site-reviews'),
                'text' => _x("It's not working for me", 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/feature-missing.svg')),
                'id' => 'feature-missing',
                'placeholder' => _x('Which feature are you looking for?', 'admin-text', 'site-reviews'),
                'text' => _x('Missing a specific feature', 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/looking-for-different.svg')),
                'id' => 'looking-for-different',
                'placeholder' => _x('What are you looking for? Maybe I can help.', 'admin-text', 'site-reviews'),
                'text' => _x('Not what I was looking for', 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/other-reason.svg')),
                'id' => 'other-reason',
                'placeholder' => _x('Could you tell me a bit more?', 'admin-text', 'site-reviews'),
                'text' => _x('Other Reason', 'admin-text', 'site-reviews'),
            ],
        ];
        return glsr()->filterArray('deactivate/reasons', $reasons);
    }

    protected function deactivateInsight(): array
    {
        $data = glsr(Cache::class)->getSystemInfo();
        $theme = wp_get_theme();
        $insight = [
            'Active Theme' => sprintf('%s v%s', (string) $theme->name, (string) $theme->version),
            'Memory Limit' => Arr::get($data, 'wp-server.fields.memory_limit.value'),
            'Multisite' => Arr::get($data, 'wp-core.fields.multisite.value'),
            'MySQL Version' => Arr::get($data, 'wp-database.fields.server_version.value'),
            'PHP Version' => Arr::get($data, 'wp-server.fields.php_version.debug'),
            'Site Language' => Arr::get($data, 'wp-core.fields.site_language.value'),
            'Timezone' => Arr::get($data, 'wp-core.fields.timezone.value'),
            'Total Users' => Arr::get($data, 'wp-core.fields.user_count.value'),
            'Website' => Arr::get($data, 'wp-core.fields.home_url.value'),
            'WordPress Version' => Arr::get($data, 'wp-core.fields.version.value'),
        ];
        return glsr()->filterArray('deactivate/insight/display', $insight, $data);
    }
}
