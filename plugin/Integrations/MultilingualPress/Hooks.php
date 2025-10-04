<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;

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
        add_action('multilingualpress.add_service_providers', function (ServiceProvidersCollection $providers) {
            // if we get this far then we know the plugin is installed.
            if ($this->isVersionSupported()) {
                $providers->add(new ServiceProvider());
            }
            // \Inpsyde\MultilingualPress\Core\PostTypeRepository::FILTER_ALL_AVAILABLE_POST_TYPES
            add_filter('multilingualpress.all_post_types',
                static fn (array $types): array => array_filter($types,
                    fn ($type) => !str_starts_with($type, glsr()->post_type),
                    \ARRAY_FILTER_USE_KEY
                )
            );
            // \Inpsyde\MultilingualPress\Core\TaxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES
            add_filter('multilingualpress.all_taxonomies',
                static function (array $taxonomies): array {
                    unset($taxonomies[glsr()->taxonomy]);
                    return $taxonomies;
                }
            );
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
            return resolve(ModuleManager::class)->isModuleActive(glsr()->id);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function isInstalled(): bool
    {
        return defined('Inpsyde\MultilingualPress\ACTION_ADD_SERVICE_PROVIDERS')
            && function_exists('Inpsyde\MultilingualPress\resolve');
    }

    protected function notify(string $name): void
    {
        add_action('network_admin_notices', function () use ($name) {
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
