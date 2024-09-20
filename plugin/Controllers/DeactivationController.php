<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\DeactivatePlugin;
use GeminiLabs\SiteReviews\Request;

class DeactivationController extends AbstractController
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
        $handle = glsr()->id.'/deactivate-plugin';
        $url = glsr()->url('assets/scripts/deactivate-plugin.js');
        wp_enqueue_script($handle, $url, ['backbone', 'underscore'], glsr()->version, [
            'in_footer' => true,
            'strategy' => 'defer',
        ]);
        wp_localize_script(glsr()->id.'/deactivate-plugin', '_glsr_deactivate', [
            'ajax' => [
                'action' => glsr()->prefix.'admin_action',
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
        $reasons = [ // order is intentional
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/confused.svg')),
                'id' => 'confused',
                'placeholder' => _x('If you would like me to help, please include your email so I can contact you.', 'admin-text', 'site-reviews'),
                'text' => _x("It's too complicated", 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/found-better.svg')),
                'id' => 'found-better',
                'placeholder' => _x('Which plugin is better?', 'admin-text', 'site-reviews'),
                'text' => _x('I found something better', 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/not-working.svg')),
                'id' => 'not-working',
                'placeholder' => _x("What isn't working? Please let me know so I can fix it.", 'admin-text', 'site-reviews'),
                'text' => _x("It's not working for me", 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/temporary.svg')),
                'id' => 'temporary',
                'placeholder' => '', // don't show the textarea
                'text' => _x("It's only temporary", 'admin-text', 'site-reviews'),
            ],
            [
                'icon' => file_get_contents(glsr()->path('assets/images/icons/feature-missing.svg')),
                'id' => 'feature-missing',
                'placeholder' => _x('Which feature are you looking for?', 'admin-text', 'site-reviews'),
                'text' => _x('Missing a specific feature', 'admin-text', 'site-reviews'),
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
        global $wpdb;
        $theme = wp_get_theme();
        $insight = [
            'Active Theme' => sprintf('%s v%s', (string) $theme->name, (string) $theme->version),
            'Memory Limit' => ini_get('memory_limit'),
            'Multisite' => is_multisite() ? 'Yes' : 'No',
            'MySQL Version' => $wpdb->get_var('SELECT VERSION()'),
            'PHP Version' => PHP_VERSION,
            'Site Language' => get_locale(),
            'Timezone' => wp_timezone_string(),
            'Total Users' => glsr_user_count(),
            'Website' => get_bloginfo('url'),
            'WordPress Version' => get_bloginfo('version'),
        ];
        return glsr()->filterArray('deactivate/insight/display', $insight);
    }
}
