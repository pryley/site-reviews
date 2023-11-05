<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Request;

class DeactivatePlugin extends AbstractCommand
{
    /** @var Request */
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(): void
    {
        $request = array_merge($this->insights(), [
            'details' => $this->request->details,
            'is_local' => Helper::isLocalServer(),
            'package_slug' => $this->request->slug,
            'package_version' => $this->request->version,
            'reason' => $this->request->reason,
        ]);
        if (empty($request['reason'])) {
            glsr_log()->warning('Deactivation reason missing.');
            return;
        }
        glsr(Api::class)->post('insights', [
            'blocking' => false,
            'body' => $request,
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 10,
            'user-agent' => sprintf('SiteReviews/%s/%s;', glsr()->version, md5(esc_url(home_url()))),
        ]);
    }

    protected function insights(): array
    {
        global $wpdb;
        $theme = wp_get_theme();
        $insight = [
            'db_version' => get_option(glsr()->prefix.'db_version'),
            'locale' => get_locale(),
            'memory_limit' => ini_get('memory_limit'),
            'multisite' => is_multisite(),
            'mysql_version' => $wpdb->get_var('SELECT VERSION()'),
            'php_version' => PHP_VERSION,
            'theme_name' => (string) $theme->name,
            'theme_slug' => (string) $theme->display('TextDomain'),
            'theme_uri' => (string) $theme->display('AuthorURI'),
            'theme_version' => (string) $theme->version,
            'timezone' => wp_timezone_string(),
            'url' => get_bloginfo('url'),
            'users' => glsr_user_count(),
            'wp_version' => get_bloginfo('version'),
        ];
        return glsr()->filterArray('deactivate/insight', $insight);
    }
}
