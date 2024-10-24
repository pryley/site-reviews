<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Install;
use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;
use GeminiLabs\SiteReviews\Role;

abstract class Controller extends AbstractController
{
    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets(): void
    {
        if ($this->isReviewAdminPage()) {
            $this->enqueueAsset('css', ['suffix' => 'admin']);
            $this->enqueueAsset('js', ['suffix' => 'admin']);
        }
    }

    /**
     * The CSS registered here will not load in the site editor unless it contains the .wp-block selector.
     *
     * @see https://github.com/WordPress/gutenberg/issues/41821
     *
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockAssets(): void
    {
        $this->registerAsset('css', ['suffix' => 'blocks']);
        $this->registerAsset('js', [
            'dependencies' => [glsr()->id.'/blocks'],
            'suffix' => 'blocks',
        ]);
    }

    /**
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets(): void
    {
        if (!glsr(AssetCss::class)->canOptimize() || !glsr(AssetCss::class)->isOptimized()) {
            $this->enqueueAsset('css');
        }
        if (!glsr(AssetJs::class)->canOptimize() || !glsr(AssetJs::class)->isOptimized()) {
            $this->enqueueAsset('js', ['in_footer' => true]);
        }
    }

    /**
     * @param array $actions
     *
     * @filter plugin_action_links_{$this->app()->id}/{$this->app()->id}.php
     */
    public function filterActionLinks($actions): array
    {
        $actions = Arr::consolidate($actions);
        if (glsr()->hasPermission('settings') && !empty($this->app()->config('settings'))) {
            $actions['settings'] = glsr(Builder::class)->a([
                'href' => glsr_admin_url('settings', 'addons', $this->app()->slug),
                'text' => _x('Settings', 'admin-text', 'site-reviews'),
            ]);
        }
        if (glsr()->hasPermission('documentation')) {
            $actions['documentation'] = glsr(Builder::class)->a([
                'data-expand' => "#addon-{$this->app()->id}",
                'href' => glsr_admin_url('documentation', 'addons'),
                'text' => _x('Help', 'admin-text', 'site-reviews'),
            ]);
        }
        return $actions;
    }

    /**
     * @filter site-reviews/capabilities
     */
    public function filterCapabilities(array $capabilities): array
    {
        if (!$this->app()->post_type) { // @phpstan-ignore-line
            return $capabilities;
        }
        $defaults = [
            'create_posts',
            'delete_others_posts',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'edit_others_posts',
            'edit_posts',
            'edit_private_posts',
            'edit_published_posts',
            'publish_posts',
            'read_private_posts',
        ];
        foreach ($defaults as $capability) {
            $capabilities[] = str_replace('post', $this->app()->post_type, $capability);
        }
        return $capabilities;
    }

    /**
     * @filter site-reviews/config
     */
    public function filterConfigPath(string $path): string
    {
        $prefix = trailingslashit($this->app()->id);
        return str_contains($path, $prefix)
            ? $prefix.str_replace($prefix, '', $path)
            : $path;
    }

    /**
     * @filter site-reviews/addon/documentation
     */
    public function filterDocumentation(array $documentation): array
    {
        $notice = glsr()->build('views/partials/addons/support-notice', [
            'addon_id' => $this->app()->id,
        ]);
        $support = $this->app()->build('views/documentation');
        $documentation[$this->app()->id] = $notice.$support;
        return $documentation;
    }

    /**
     * @filter site-reviews/path
     */
    public function filterFilePaths(string $path, string $file): string
    {
        $addonPrefix = trailingslashit($this->app()->id);
        return str_starts_with($file, $addonPrefix)
            ? $this->app()->path(Str::replaceFirst($addonPrefix, '', $file))
            : $path;
    }

    /**
     * @param string $translation
     * @param string $single
     *
     * @filter gettext_{$this->app()->id}
     */
    public function filterGettext($translation, $single): string
    {
        $translation = Cast::toString($translation);
        return glsr(Translator::class)->translate($translation, $this->app()->id, [
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $context
     *
     * @filter gettext_with_context_{$this->app()->id}
     */
    public function filterGettextWithContext($translation, $single, $context): string
    {
        $translation = Cast::toString($translation);
        return glsr(Translator::class)->translate($translation, $this->app()->id, [
            'context' => Cast::toString($context),
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @filter site-reviews/enqueue/public/localize
     */
    public function filterLocalizedPublicVariables(array $variables): array
    {
        $variables['addons'][$this->app()->id] = null;
        return $variables;
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int    $number
     *
     * @filter ngettext_{$this->app()->id}
     */
    public function filterNgettext($translation, $single, $plural, $number): string
    {
        $translation = Cast::toString($translation);
        return glsr(Translator::class)->translate($translation, $this->app()->id, [
            'number' => Cast::toInt($number),
            'plural' => Cast::toString($plural),
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int    $number
     * @param string $context
     *
     * @filter ngettext_with_context_{$this->app()->id}
     */
    public function filterNgettextWithContext($translation, $single, $plural, $number, $context): string
    {
        $translation = Cast::toString($translation);
        return glsr(Translator::class)->translate($translation, $this->app()->id, [
            'context' => Cast::toString($context),
            'number' => Cast::toInt($number),
            'plural' => Cast::toString($plural),
            'single' => Cast::toString($single),
        ]);
    }

    /**
     * @filter {$this->app()->id}/render/view
     */
    public function filterRenderView(string $view): string
    {
        $style = glsr(OptionManager::class)->get('settings.general.style', 'default');
        $styledView = sprintf('views/styles/%s/%s', $style, basename($view));
        if (file_exists($this->app()->file($styledView))) {
            return $styledView;
        }
        return $view;
    }

    /**
     * @filter site-reviews/roles
     */
    public function filterRoles(array $roles): array
    {
        if (!$this->app()->post_type) { // @phpstan-ignore-line
            return $roles;
        }
        $defaults = [
            'administrator' => [
                'create_posts',
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
            ],
            'editor' => [
                'create_posts',
                'delete_others_posts',
                'delete_posts',
                'delete_private_posts',
                'delete_published_posts',
                'edit_others_posts',
                'edit_posts',
                'edit_private_posts',
                'edit_published_posts',
                'publish_posts',
                'read_private_posts',
            ],
            'author' => [
                'create_posts',
                'delete_posts',
                'delete_published_posts',
                'edit_posts',
                'edit_published_posts',
                'publish_posts',
            ],
            'contributor' => [
                'delete_posts',
                'edit_posts',
            ],
        ];
        foreach ($defaults as $role => $capabilities) {
            if (!array_key_exists($role, $roles)) {
                continue;
            }
            foreach ($capabilities as $capability) {
                $roles[$role][] = str_replace('post', $this->app()->post_type, $capability);
            }
        }
        return $roles;
    }

    /**
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge($this->app()->config('settings'), $settings);
    }

    /**
     * @filter site-reviews/addon/subsubsub
     */
    public function filterSubsubsub(array $subsubsub): array
    {
        return $subsubsub;
    }

    /**
     * @filter site-reviews/translation/entries
     */
    public function filterTranslationEntries(array $entries): array
    {
        $potFile = $this->app()->path("{$this->app()->languages}/{$this->app()->id}.pot");
        return glsr(Translation::class)->extractEntriesFromPotFile($potFile, $this->app()->id, $entries);
    }

    /**
     * @filter site-reviews/translator/domains
     */
    public function filterTranslatorDomains(array $domains): array
    {
        return [...$domains, $this->app()->id];
    }

    /**
     * @action {$this->app()->id}/activated
     */
    public function install(): void
    {
    }

    /**
     * @action admin_init
     */
    public function onActivation(): void
    {
        $option = glsr()->prefix."activated_{$this->app()->id}";
        if (empty(get_option($option))) {
            update_option($option, true);
            if ($this->app()->post_type) { // @phpstan-ignore-line
                glsr(Role::class)->reset($this->filterRoles([
                    'administrator' => [],
                    'author' => [],
                    'contributor' => [],
                    'editor' => [],
                ]));
            }
            $this->app()->action('activated');
        }
    }

    /**
     * @action deactivate_{$this->app()->basename}
     */
    public function onDeactivation(bool $isNetworkDeactivation): void
    {
        $option = glsr()->prefix."activated_{$this->app()->id}";
        if (!$isNetworkDeactivation) {
            delete_option($option);
            $this->app()->action('deactivated');
            return;
        }
        foreach (glsr(Install::class)->sites() as $siteId) {
            switch_to_blog($siteId);
            delete_option($option);
            $this->app()->action('deactivated');
            restore_current_blog();
        }
    }

    /**
     * @action init
     */
    public function registerBlocks(): void
    {
    }

    /**
     * @action init
     */
    public function registerLanguages(): void
    {
        $path = plugin_basename($this->app()->path());
        $path = trailingslashit("{$path}/{$this->app()->languages}");
        load_plugin_textdomain($this->app()->id, false, $path);
    }

    /**
     * @action init
     */
    public function registerShortcodes(): void
    {
    }

    /**
     * @action admin_init
     */
    public function registerTinymcePopups(): void
    {
    }

    /**
     * @action widgets_init
     */
    public function registerWidgets(): void
    {
    }

    /**
     * @action site-reviews/settings/{$this->app()->slug}
     */
    public function renderSettings(string $rows): void
    {
        glsr(Template::class)->render("{$this->app()->id}/views/settings", [
            'context' => [
                'rows' => $rows,
                'title' => $this->app()->name,
            ],
        ]);
    }

    /**
     * @action plugins_loaded
     */
    public function runIntegrations(): void
    {
        $dir = $this->app()->path('plugin/Integrations');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        $namespace = (new \ReflectionClass($this->app()))->getNamespaceName();
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                continue;
            }
            try {
                $hooks = "{$namespace}\Integrations\\{$fileinfo->getBasename()}\Hooks";
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks);
                    glsr($hooks)->run();
                    glsr($hooks)->runDeferred();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    protected function buildAssetArgs(string $ext, array $args = []): array
    {
        $args = wp_parse_args($args, [
            'defer' => true,
            'in_footer' => false,
            'suffix' => '',
        ]);
        $suffix = Str::prefix($args['suffix'], '-');
        $path = "assets/{$this->app()->id}{$suffix}.{$ext}";
        if (!file_exists($this->app()->path($path)) || !in_array($ext, ['css', 'js'])) {
            return [];
        }
        $suffix = Str::prefix($args['suffix'], '/');
        $dependencies = Arr::get($args, 'dependencies', [glsr()->id.$suffix]);
        $handle = $this->app()->id.$suffix;
        $funcArgs = [
            $handle,
            $this->app()->url($path),
            Arr::consolidate($dependencies),
            $this->app()->version,
        ];
        if ('js' === $ext && wp_validate_boolean($args['in_footer'])) {
            $funcArgs[] = true; // load script in the footer
        }
        return $funcArgs;
    }

    protected function enqueueAsset(string $extension, array $args = []): void
    {
        $defer = Arr::get($args, 'defer', false);
        if ($args = $this->buildAssetArgs($extension, $args)) {
            $function = 'js' === $extension
                ? 'wp_enqueue_script'
                : 'wp_enqueue_style';
            call_user_func_array($function, $args);
            if (wp_validate_boolean($defer)) {
                wp_script_add_data($args[0], 'strategy', 'defer');
            }
        }
    }

    protected function registerAsset(string $extension, array $args = []): void
    {
        $defer = Arr::get($args, 'defer', false);
        if ($args = $this->buildAssetArgs($extension, $args)) {
            $function = 'js' === $extension
                ? 'wp_register_script'
                : 'wp_register_style';
            call_user_func_array($function, $args);
            if (wp_validate_boolean($defer)) {
                wp_script_add_data($args[0], 'strategy', 'defer');
            }
        }
    }
}
