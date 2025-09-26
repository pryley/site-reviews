<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\FrontendRelationController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\RelationController;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\PluginProperties;

use function Inpsyde\MultilingualPress\resolve;

class Hooks extends IntegrationHooks
{
    public function levelPluginsLoaded(): ?int
    {
        return 0;
    }

    /**
     * The "multilingualpress.modules" hook is triggered on plugins_loaded:0
     * so we need to load hooks at or earlier than that.
     *
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
            $this->notify('MultilingualPress');
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
            ['filterRemotePostData', 'multilingualpress.new_relate_remote_post_before_insert', 10, 3],
            ['filterSyncedMetaKeys', 'multilingualpress.sync_post_meta_keys', 10, 2],
            ['filterTaxonomiesIsChecked', 'multilingualpress.copy_taxonomies_is_checked'],
        ]);
        $this->hook(FrontendRelationController::class, [
            ['onCreatedReview', 'site-reviews/review/created', 20],
            ['onTransitioned', 'site-reviews/review/transitioned', 10, 3],
            ['onUpdatedReview', 'site-reviews/review/updated', 20],
        ]);
        $this->hook(RelationController::class, [
            ['onBulkEditReviews', 'bulk_edit_posts', 10, 2],
            ['onPinned', 'site-reviews/review/pinned', 10, 2],
            ['onRelateReview', 'multilingualpress.metabox_after_relate_posts', 10, 2],
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
