<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\PluginProperties;

use function Inpsyde\MultilingualPress\resolve;

class Hooks extends IntegrationHooks
{
    /**
     * The "multilingualpress.modules" hook is triggered on plugins_loaded:0
     * so we need to run earlier than that.
     */
    public function levelPluginsLoaded(): ?int
    {
        return -10;
    }

    /**
     * @action plugins_loaded:-10
     */
    public function onPluginsLoaded(): void
    {
        add_filter('multilingualpress.modules', function (array $modules): array {
            // if we get this far then we know the plugin is installed.
            if ($this->isVersionSupported()) {
                $modules[] = new ServiceProvider();
            }
            return $modules;
        });
    }

    /**
     * Hooks are run in the ServiceProvider on module activation.
     */
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('MultilingualPress');
            return;
        }
    }

    protected function isEnabled(): bool
    {
        if (!$this->isInstalled()) {
            return false;
        }
        try {
            return resolve(ModuleManager::class)->isModuleActive(ServiceProvider::MODULE_ID);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function isInstalled(): bool
    {
        return function_exists('\Inpsyde\MultilingualPress\resolve');
    }

    protected function notify(string $name): void
    {
        add_action('network_admin_notices', function () {
            $notice = _x('Update %s to version %s or higher to enable the integration with Site Reviews.', 'admin-text', 'site-reviews');
            $supportedVersion = sanitize_text_field($this->supportedVersion());
            $text = sprintf($notice, $name, $supportedVersion);
            echo glsr(Builder::class)->div([
                'class' => 'notice notice-warning is-dismissible',
                'text' => wpautop($text),
            ]);
        });
    }

    protected function supportedVersion(): string
    {
        return '4.5.0';
    }

    protected function version(): string
    {
        try {
            return resolve(PluginProperties::class)->version();
        } catch (\Exception $e) {
            return '';
        }
    }
}
