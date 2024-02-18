<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Metabox\MetaboxFields;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\TranslationUi\Post;

class ServiceProvider implements ModuleServiceProvider
{
    public const MODULE_ID = 'site-reviews';

    /**
     * @param Container $container
     */
    public function activateModule(Container $container)
    {
        $this->addMetaboxes();
    }

    /**
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer
     */
    public function register(Container $container)
    {
        $this->removeGlobalSettings();
        $moduleManager = $container[ModuleManager::class];
        if (!$moduleManager->isModuleActive(self::MODULE_ID)) {
            $this->removeSupport();
        }
    }

    /**
     * @throws \Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(self::MODULE_ID, [
                'description' => _x('Enable Site Reviews Support for MultilingualPress.', 'admin-text', 'site-reviews'),
                'name' => glsr()->name,
                'active' => true,
                'disabled' => false,
            ])
        );
    }

    protected function addMetaboxes()
    {
        add_filter(Post\Metabox::HOOK_PREFIX.'tabs', static function (array $tabs): array {
            foreach ($tabs as $index => $tab) {
                if (Post\MetaboxFields::TAB_TAXONOMIES !== $tab->id()) {
                    continue;
                }
                $fields = array_merge(
                    (new MetaboxFields())->fields(),
                    $tab->fields()
                );
                $tabs[$index] = new Post\MetaboxTab(
                    Post\MetaboxFields::TAB_TAXONOMIES,
                    glsr()->name,
                    ...$fields
                );
            }
            return $tabs;
        });
    }

    protected function removeGlobalSettings(): void
    {
        add_filter(PostTypeRepository::FILTER_ALL_AVAILABLE_POST_TYPES, function (array $supported) {
            unset($supported[glsr()->post_type]);
            return $supported;
        });
        add_filter(TaxonomyRepository::FILTER_ALL_AVAILABLE_TAXONOMIES, function (array $supported) {
            unset($supported[glsr()->taxonomy]);
            return $supported;
        });
    }

    protected function removeSupport(): void
    {
        add_filter(PostTypeRepository::FILTER_SUPPORTED_POST_TYPES, function (array $supported) {
            unset($supported[glsr()->post_type]);
            return $supported;
        });
        add_filter(TaxonomyRepository::FILTER_SUPPORTED_TAXONOMIES, function (array $supported) {
            unset($supported[glsr()->taxonomy]);
            return $supported;
        });
    }

}
