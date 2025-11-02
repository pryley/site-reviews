<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\RelationController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers\TrasherController;
use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Editor\Notices\ExistingAttachmentsNotice;
use Inpsyde\MultilingualPress\Framework\Filesystem;
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
            ['enforceEntitySupport', 'multilingualpress.update_plugin_settings', 20],
            ['filterAdminInlineCss', 'site-reviews/enqueue/admin/inline-styles'],
            ['filterContentIsChecked', 'multilingualpress.copy_content_is_checked'],
            ['filterForceCreateRelations', 'multilingualpress.force_create_post_relations'],
            ['filterForceCreateRelations', 'multilingualpress.force_create_term_relations'],
            ['filterGettext', 'gettext_multilingualpress', 10, 2],
            ['filterMetaboxTabs', 'multilingualpress.post_translation_metabox_tabs', 10, 2],
            ['filterNotices', 'site-reviews/notices'],
            ['filterPostStatuses', 'multilingualpress.translation_ui_post_statuses'],
            ['filterSettingForm', 'site-reviews/setting-form/config'],
            ['filterTaxonomiesIsChecked', 'multilingualpress.copy_taxonomies_is_checked'],
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
        glsr(Hooks::class)->hook(TrasherController::class, [
            ['syncDelete', 'delete_post'],
            ['syncTrash', 'untrashed_post'],
            ['syncTrash', 'trashed_post'],
            ['removeDefaultTrasher', 'current_screen'],
        ]);
        glsr()->action('multilingualpress/activate', $container);
    }

    /**
     * Register module services on the given container.
     */
    public function register(Container $container): void
    {
        if (!$container->get(ModuleManager::class)->isModuleActive(glsr()->id)) {
            $this->removeSupportedEntities();
        }
        $container->share(ImageCopier::class, static function (Container $container): ImageCopier {
            return new ImageCopier(
                $container->get(Filesystem::class),
                $container->get(ExistingAttachmentsNotice::class)
            );
        });
        // very hacky class override...
        $property = (new \ReflectionClass($container))->getProperty('values');
        $property->setAccessible(true);
        $values = $property->getValue($container);
        $values[Copier::class] = $this->copierOverride($container);
        $property->setValue($container, $values);
        glsr()->action('multilingualpress/register', $container);
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

    protected function copierOverride(Container $container): Copier
    {
        $copier = $container->get(Copier::class);
        return new class($container, $copier) extends Copier {
            private Container $container;
            private Copier $copier;

            public function __construct(Container $container, Copier $copier)
            {
                $this->container = $container;
                $this->copier = $copier;
                parent::__construct(
                    $container->get('wpdb'),
                    $container->get(Filesystem::class),
                    $container->get(ExistingAttachmentsNotice::class)
                );
            }

            public function copyById(int $sourceSiteId, int $remoteSiteId, array $sourceAttachmentIds): array
            {
                $attachmentIds = [];
                $reviewAttachmentIds = array_filter($sourceAttachmentIds,
                    fn ($attachmentId) => str_contains((string) get_attached_file($attachmentId, true), 'site-reviews/')
                );
                if (!empty($reviewAttachmentIds)) {
                    $attachmentIds = $this->container->get(ImageCopier::class)->copyById($sourceSiteId, $remoteSiteId, $reviewAttachmentIds);
                }
                if ($otherAttachmentIds = array_diff($sourceAttachmentIds, $reviewAttachmentIds)) {
                    $copiedAttachmentIds = $this->copier->copyById($sourceSiteId, $remoteSiteId, $otherAttachmentIds);
                    return array_merge($attachmentIds, $copiedAttachmentIds);
                }
                return $attachmentIds;
            }
        };
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
