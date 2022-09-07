<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;

class DeactivatePlugin implements Contract
{
    protected const API_URL = 'https://api.site-reviews.com/v1/insights';

    /**
     * @var Request
     */
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function handle()
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
        wp_remote_post(static::API_URL, [
            'blocking' => false,
            'body' => $request,
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 10,
            'user-agent' => sprintf('SiteReviews/%s/%s;', glsr()->version, md5(esc_url(home_url()))),
        ]);
    }

    protected function insights(): array
    {
        $data = glsr(Cache::class)->getSystemInfo();
        $theme = wp_get_theme();
        $insight = [
            'db_version' => get_option(glsr()->prefix.'db_version'),
            'locale' => Arr::get($data, 'wp-core.fields.site_language.value'),
            'memory_limit' => Arr::get($data, 'wp-server.fields.memory_limit.value'),
            'multisite' => Arr::get($data, 'wp-core.fields.multisite.debug'),
            'mysql_version' => Arr::get($data, 'wp-database.fields.server_version.value'),
            'php_version' => Arr::get($data, 'wp-server.fields.php_version.debug'),
            'theme_name' => (string) $theme->name,
            'theme_slug' => (string) $theme->display('TextDomain'),
            'theme_uri' => (string) $theme->display('AuthorURI'),
            'theme_version' => (string) $theme->version,
            'timezone' => Arr::get($data, 'wp-core.fields.timezone.value'),
            'url' => Arr::get($data, 'wp-core.fields.home_url.value'),
            'users' => Arr::get($data, 'wp-core.fields.user_count.value'),
            'wp_version' => Arr::get($data, 'wp-core.fields.version.value'),
        ];
        return glsr()->filterArray('deactivate/insight', $insight, $data);
    }
}
