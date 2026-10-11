<?php

namespace GeminiLabs\SiteReviews\Connect\Commands;

use GeminiLabs\SiteReviews\Connect\LicenseRow;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Request;

/**
 * The "Install Site Reviews Premium" button of the License Key row.
 */
class Authorize extends AbstractLicenseCommand
{
    public const CONNECT_HOSTS = ['niftyplugins.com', 'site-reviews.com'];
    public const TOKEN_LIFETIME = 15 * MINUTE_IN_SECONDS;

    public array $check;
    public string $connectUrl = '';
    public string $license = '';
    public string $token = '';

    /**
     * @param array $check the licence server's answer when Verify has just made it;
     *                     otherwise the saved key is verified again here
     */
    public function __construct(array $check = [])
    {
        $this->check = $check;
        $this->license = glsr(LicenseRow::class)->savedKey();
    }

    public function handle(): void
    {
        if (!$this->canInstall()) {
            return;
        }
        if (empty($this->check)) {
            $verify = new Verify(new Request(['connect' => false, 'license' => $this->license]));
            $verify->handle();
            if (!$verify->successful()) {
                if ('license_server' !== substr($verify->code, strlen(glsr()->prefix))) {
                    glsr(LicenseRow::class)->flush(); // the row is redrawn with what the server said now
                    $this->redraw = true;
                }
                $this->refuse(substr($verify->code, strlen(glsr()->prefix)), $verify->message, $verify->link, $verify->status, $verify->type);
                return;
            }
            $this->check = $verify->check;
        }
        $updater = glsr(LicenseRow::class)->updater($this->license, true);
        if (!$this->checkConnectUrl($this->check['connect_url'])
            || !$this->checkVersions($updater->version())
            || !$this->checkFilesystem()
            || !$this->checkAddresses()) {
            return;
        }
        $this->token = $this->createToken();
    }

    public function response(): array
    {
        if (!$this->successful()) {
            return parent::response();
        }
        return [
            'fields' => [
                'ajax' => admin_url('admin-ajax.php'),
                'endpoint' => rest_url(glsr()->id.'/v1/premium/install'),
                'license' => $this->license,
                'return' => glsr_admin_url('settings', 'general'),
                'site' => Url::license(),
                'token' => $this->token,
            ],
            'url' => $this->connectUrl,
        ];
    }

    /**
     * The database holds the hash, never the token the store is given.
     */
    public static function tokenKey(string $token): string
    {
        return glsr()->prefix.'premium_token_'.hash('sha256', $token);
    }

    protected function canInstall(): bool
    {
        foreach (['install_plugins', 'activate_plugins', 'manage_options'] as $capability) {
            if (!current_user_can($capability)) {
                return $this->refuse('cannot_install',
                    _x('You are not allowed to install plugins on this site.', 'admin-text', 'site-reviews'), [], 403
                );
            }
        }
        if (!wp_is_file_mod_allowed('site_reviews_premium_install')) {
            return $this->refuse('cannot_install',
                _x('Plugin installation is disabled on this site.', 'admin-text', 'site-reviews'), [], 403
            );
        }
        if ('' === $this->license) {
            return $this->refuse('license_missing',
                _x('Please verify your license key first.', 'admin-text', 'site-reviews')
            );
        }
        return true;
    }

    /**
     * The store only posts to the licence URL's host, or a subdomain of it.
     */
    protected function checkAddresses(): bool
    {
        $host = strtolower((string) wp_parse_url(Url::license(), \PHP_URL_HOST));
        foreach ([rest_url(), admin_url()] as $url) {
            $other = strtolower((string) wp_parse_url($url, \PHP_URL_HOST));
            if ('' === $host || ($host !== $other && !str_ends_with($other, '.'.$host))) {
                return $this->refuse('site_addresses',
                    _x('This site\'s addresses are on different hosts, so the store cannot send you back to it.', 'admin-text', 'site-reviews'),
                    $this->manualInstallLink()
                );
            }
        }
        return true;
    }

    protected function checkConnectUrl(string $url): bool
    {
        $host = strtolower((string) wp_parse_url($url, \PHP_URL_HOST));
        $host = (string) preg_replace('|^www\.|', '', $host);
        $hosts = glsr()->filterArrayUnique('premium/connect-hosts', static::CONNECT_HOSTS);
        if ('https' !== wp_parse_url($url, \PHP_URL_SCHEME) || !in_array($host, $hosts, true)) {
            return $this->refuse('connect_page',
                _x('The license server did not name a page to install from. Please install Site Reviews Premium manually.', 'admin-text', 'site-reviews'),
                $this->manualInstallLink()
            );
        }
        $this->connectUrl = $url;
        return true;
    }

    /**
     * The store cannot ask for FTP credentials.
     */
    protected function checkFilesystem(): bool
    {
        require_once ABSPATH.'wp-admin/includes/file.php';
        ob_start();
        $credentials = request_filesystem_credentials(glsr_admin_url('settings'), '', false, false, null);
        ob_end_clean();
        if (false === $credentials || !\WP_Filesystem($credentials)) {
            return $this->refuse('filesystem',
                _x('Site Reviews Premium cannot be installed from here because WordPress needs FTP credentials to write files on this site.', 'admin-text', 'site-reviews'),
                $this->manualInstallLink()
            );
        }
        return true;
    }

    protected function checkVersions(array $version): bool
    {
        if ('' !== $version['requires'] && version_compare(get_bloginfo('version'), $version['requires'], '<')) {
            return $this->refuse('wordpress_version',
                sprintf(_x('Site Reviews Premium requires WordPress %s or later.', 'admin-text', 'site-reviews'), $version['requires'])
            );
        }
        if ('' !== $version['requires_php'] && version_compare(\PHP_VERSION, $version['requires_php'], '<')) {
            return $this->refuse('php_version',
                sprintf(_x('Site Reviews Premium requires PHP %s or later.', 'admin-text', 'site-reviews'), $version['requires_php'])
            );
        }
        return true;
    }

    protected function createToken(): string
    {
        $token = wp_generate_password(32, false);
        set_transient(static::tokenKey($token), [
            'created' => time(),
            'user_id' => get_current_user_id(),
        ], static::TOKEN_LIFETIME);
        return $token;
    }
}
