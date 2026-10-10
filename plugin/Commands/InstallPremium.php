<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;
use GeminiLabs\SiteReviews\Request;

/**
 * The store's call to premium/install, authorised by the token that premium/connect made.
 */
class InstallPremium extends AbstractCommand
{
    public const PLUGIN_FILE = PremiumLicense::PLUGIN_FILE;

    /**
     * Premium reads and deletes this to say once that it was installed from here.
     */
    public const INSTALLED_KEY = 'glsr_premium_installed';

    public string $code = '';
    public array $link = [];
    public string $message = '';
    public int $status = 200;
    public string $token;

    public function __construct(Request $request)
    {
        $this->token = sanitize_text_field((string) $request->get('token', ''));
    }

    public function handle(): void
    {
        if (!$this->claimToken()) {
            $this->refuse('token',
                _x('This install request has been used or has expired. Please start again from your website.', 'admin-text', 'site-reviews'),
                [], 403
            );
            return;
        }
        if (!wp_is_file_mod_allowed('site_reviews_premium_install')) {
            $this->refuse('cannot_install',
                _x('Plugin installation is disabled on this site.', 'admin-text', 'site-reviews'),
                [], 403
            );
            return;
        }
        require_once ABSPATH.'wp-admin/includes/file.php';
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        if (!glsr(PremiumLicense::class)->isOnDisk() && !$this->install()) {
            return;
        }
        if ($this->activate()) {
            set_transient(static::INSTALLED_KEY, time(), 5 * MINUTE_IN_SECONDS);
        }
    }

    public function response(): array
    {
        if (!$this->successful()) {
            return array_filter([
                'code' => $this->code,
                'link' => $this->link,
                'message' => $this->message,
            ]);
        }
        return [
            'installed' => true,
            'message' => _x('Site Reviews Premium is installed and active.', 'admin-text', 'site-reviews'),
        ];
    }

    protected function activate(): bool
    {
        $result = $this->silenced(fn () => activate_plugin(static::PLUGIN_FILE, '', false, false));
        if (is_wp_error($result)) {
            return $this->refuse('activation',
                sprintf(_x('Site Reviews Premium is installed but could not be activated: %s', 'admin-text', 'site-reviews'), $result->get_error_message()),
                $this->link(admin_url('plugins.php'), __('Plugins')), 500
            );
        }
        return true;
    }

    /**
     * A token is good for one call: a failed deletion means another call got there first.
     */
    protected function claimToken(): bool
    {
        if ('' === $this->token) {
            return false;
        }
        $key = ConnectPremium::tokenKey($this->token);
        $record = get_transient($key);
        if (!is_array($record) || !delete_transient($key)) {
            return false;
        }
        wp_set_current_user((int) ($record['user_id'] ?? 0));
        return true;
    }

    /**
     * Downloaded here rather than by the upgrader, which loses the HTTP status of a failed download.
     */
    protected function install(): bool
    {
        $package = $this->package();
        if ('' === $package) {
            return false;
        }
        $file = download_url($package);
        if (is_wp_error($file)) {
            if (401 === (int) ($file->get_error_data()['code'] ?? 0)) {
                return $this->refuse('download',
                    _x('The license server refused the download: your license is no longer valid for this website.', 'admin-text', 'site-reviews'),
                    $this->licenseKeysLink(), 500
                );
            }
            return $this->refuseInstall($file->get_error_message());
        }
        // hooked by admin-filters.php over admin-ajax, it would print into the JSON body
        remove_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20);
        $skin = new \WP_Ajax_Upgrader_Skin();
        $upgrader = new \Plugin_Upgrader($skin);
        $result = $this->silenced(fn () => $upgrader->install($file));
        if (file_exists($file)) {
            unlink($file); // the upgrader deletes only the packages it downloaded itself
        }
        if (true === $result) {
            return true;
        }
        $message = $skin->get_error_messages();
        if ('' === $message && is_wp_error($result)) {
            $message = $result->get_error_message();
        }
        return $this->refuseInstall($message);
    }

    protected function licenseKeysLink(): array
    {
        return $this->link(glsr_premium_url('license-keys'), _x('Open the License Keys page of your account', 'admin-text', 'site-reviews'));
    }

    protected function link(string $url, string $text): array
    {
        return compact('text', 'url');
    }

    protected function manualInstallLink(): array
    {
        return $this->link(glsr_premium_url('account'), _x('Download Site Reviews Premium from your account and install it manually', 'admin-text', 'site-reviews'));
    }

    protected function package(): string
    {
        $updater = new Updater(ConnectPremium::ADDON_ID, ['force' => true]);
        $version = $updater->version();
        $host = strtolower((string) wp_parse_url($version['package'], \PHP_URL_HOST));
        if ('' !== $host && $host === strtolower((string) wp_parse_url($updater->apiUrl, \PHP_URL_HOST))) {
            return $version['package'];
        }
        $this->refuse('package',
            '' !== $version['msg']
                ? $version['msg']
                : _x('The license server did not provide a download for your license.', 'admin-text', 'site-reviews'),
            $this->licenseKeysLink(), 500
        );
        return '';
    }

    protected function refuse(string $code, string $message, array $link = [], int $status = 400): bool
    {
        $this->code = glsr()->prefix.$code;
        $this->link = $link;
        $this->message = $message;
        $this->status = $status;
        $this->fail();
        return false;
    }

    protected function refuseInstall(string $reason): bool
    {
        return $this->refuse('download',
            sprintf(_x('Site Reviews Premium could not be installed: %s', 'admin-text', 'site-reviews'), $reason ?: _x('the download failed.', 'admin-text', 'site-reviews')),
            $this->manualInstallLink(), 500
        );
    }

    /**
     * WP_Upgrader_Skin::error() can leave an output buffer open.
     *
     * @return mixed
     */
    protected function silenced(callable $callback)
    {
        $level = ob_get_level();
        ob_start();
        $result = $callback();
        while (ob_get_level() > $level) {
            ob_end_clean();
        }
        return $result;
    }
}
