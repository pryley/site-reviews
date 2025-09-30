<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\RelationController;
use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;

class ServiceProvider implements ModuleServiceProvider
{
    /**
     * Register hooks on module activation.
     *
     * MultilingualPress defines hook names in class constants but we use the
     * hook names directly instead of using constants because the likelihood of
     * hook name changes are far less than changes to the plugin architecture.
     */
    public function activateModule(Container $container): void // @phpstan-ignore-line class.notFound
    {
        glsr(Hooks::class)->hook(Controller::class, [
            ['filterAdminInlineCss', 'site-reviews/enqueue/admin/inline-styles'],
            ['filterAdminInlineJs', 'site-reviews/enqueue/admin/inline-script'],
            ['filterContentIsChecked', 'multilingualpress.copy_content_is_checked'],
            ['filterGettext', 'gettext_multilingualpress', 10, 2],
            ['filterMetaboxTabs', 'multilingualpress.post_translation_metabox_tabs', 10, 2],
            ['filterPostStatuses', 'multilingualpress.translation_ui_post_statuses'],
            ['filterSettingForm', 'site-reviews/setting-form/config'],
            ['filterTaxonomiesIsChecked', 'multilingualpress.copy_taxonomies_is_checked'],
            ['renderNotices', 'admin_head'],
        ]);
        glsr(Hooks::class)->hook(RelationController::class, [
            ['filterSyncedMetaKeys', 'multilingualpress.sync_post_meta_keys', 10, 2],
            ['onBulkEditReviews', 'bulk_edit_posts', 10, 2],
            ['onFrontendCreated', 'site-reviews/review/created', 20],
            ['onFrontendTransitioned', 'site-reviews/review/transitioned', 10, 3],
            ['onFrontendUpdated', 'site-reviews/review/updated', 20],
            ['onGeolocated', 'site-reviews/review/geolocated', 10, 2],
            ['onPinned', 'site-reviews/review/pinned', 10, 2],
            ['onResponded', 'site-reviews/review/responded', 10, 2],
            ['onSettingsUpdated', 'site-reviews/settings/updated'],
            ['onSyncReview', 'multilingualpress.metabox_after_relate_posts', 10, 2],
            ['onVerified', 'site-reviews/review/verified'],
        ]);
    }

    /**
     * Register module services on the given container.
     */
    public function register(Container $container): void
    {
        $this->removeAvailableEntities();
        if (!$container->get(ModuleManager::class)->isModuleActive(glsr()->id)) {
            $this->removeSupportedEntities();
        }
        // $container->addService(
        //     FieldCopier::class,
        //     static fn () => new FieldCopier($container->get(Copier::class))
        // );
        // $container->share(
        //     Filesystem::class,
        //     static function (): Filesystem {
        //         return new Filesystem();
        //     }
        // );
    }

    /**
     * Register the module with the module manager.
     *
     * @throws \Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(glsr()->id, [
                'description' => _x('Enable Site Reviews Support for MultilingualPress.', 'admin-text', 'site-reviews'),
                'name' => glsr()->name,
                'active' => true,
                'disabled' => false,
            ])
        );
    }

    protected function removeAvailableEntities(): void
    {
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
    }

    protected function removeSupportedEntities(): void
    {
        // \Inpsyde\MultilingualPress\Core\PostTypeRepository::FILTER_SUPPORTED_POST_TYPES
        add_filter('multilingualpress.supported_post_types',
            static fn (array $types): array => array_filter($types,
                fn ($type) => !str_starts_with($type, glsr()->post_type),
                \ARRAY_FILTER_USE_KEY
            )
        );
        // \Inpsyde\MultilingualPress\Core\TaxonomyRepository::FILTER_SUPPORTED_TAXONOMIES
        add_filter('multilingualpress.supported_taxonomies',
            static function (array $taxonomies): array {
                unset($taxonomies[glsr()->taxonomy]);
                return $taxonomies;
            }
        );
    }
}
