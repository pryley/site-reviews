<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\PluginProperties;

use function Inpsyde\MultilingualPress\resolve;

class Hooks extends AbstractHooks
{
    public function hasPluginsLoaded(): bool
    {
        return true;
    }

    /**
     * @action plugins_loaded:0
     */
    public function onPluginsLoaded(): void
    {
        add_filter('multilingualpress.modules', function (array $modules): array {
            if ($this->isVersionSupported()) {
                $modules[] = new ServiceProvider();
            }
            return $modules;
        });
    }

    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->unsupportedVersionNotice();
            return;
        }
        if (!$this->isEnabled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterAdminInlineCss', 'site-reviews/enqueue/admin/inline-styles'],
            ['filterAdminInlineJs', 'site-reviews/enqueue/admin/inline-script'],
            ['filterContentIsChecked', 'multilingualpress.copy_content_is_checked'], // bool
            ['filterGettext', 'gettext_multilingualpress', 10, 2],
            ['filterPostStatuses', 'multilingualpress.translation_ui_post_statuses'], // ['pending', 'publish']
            ['filterTaxonomiesIsChecked', 'multilingualpress.copy_taxonomies_is_checked'],
            ['onBulkEditReviews', 'bulk_edit_posts', 10, 2],
            ['onCreatedReview', 'site-reviews/review/created', 20],
            ['onPinned', 'site-reviews/review/pinned', 10, 2],
            ['onRelateReview', 'multilingualpress.metabox_after_relate_posts', 10, 2],
            ['onTransitioned', 'site-reviews/review/transitioned', 10, 3],
            ['onUpdatedReview', 'site-reviews/review/updated', 20],
            ['onVerified', 'site-reviews/review/verified'],
        ]);
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

    protected function isVersionSupported(): bool
    {
        if (!$this->isInstalled()) {
            return false;
        }
        try {
            return version_compare(resolve(PluginProperties::class)->version(), '4.5.0', '>=');
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function unsupportedVersionNotice(): void
    {
        add_action('network_admin_notices', function () {
            $notice = _x('Please update MultilingualPress to the latest version to enable the Site Reviews integration.', 'admin-text', 'site-reviews');
            echo glsr(Builder::class)->div([
                'class' => 'notice notice-warning is-dismissible',
                'text' => wpautop($notice),
            ]);
        });
    }
}
