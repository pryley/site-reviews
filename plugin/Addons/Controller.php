<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

abstract class Controller extends BaseController
{
    /**
     * @var Addon
     */
    protected $addon;

    public function __construct()
    {
        $this->setAddon();
    }

    /**
     * @return void
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminAssets()
    {
        if ($this->isReviewAdminPage()) {
            $this->enqueueAsset('css', ['suffix' => 'admin']);
            $this->enqueueAsset('js', ['suffix' => 'admin']);
        }
    }

    /**
     * The CSS registered here will not load in the site editor unless it contains the .wp-block selector.
     * @see https://github.com/WordPress/gutenberg/issues/41821
     * @return void
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockAssets()
    {
        $this->registerAsset('css', ['suffix' => 'blocks']);
        $this->registerAsset('js', [
            'dependencies' => [glsr()->id.'/blocks'],
            'suffix' => 'blocks',
        ]);
    }

    /**
     * @return void
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets()
    {
        if (!glsr(AssetCss::class)->canOptimize() || !glsr(AssetCss::class)->isOptimized()) {
            $this->enqueueAsset('css');
        }
        if (!glsr(AssetJs::class)->canOptimize() || !glsr(AssetJs::class)->isOptimized()) {
            $this->enqueueAsset('js', ['in_footer' => true]);
        }
    }

    /**
     * @return array
     * @filter plugin_action_links_{addon_id}/{addon_id}.php
     */
    public function filterActionLinks(array $links)
    {
        if (glsr()->hasPermission('settings') && !empty($this->addon->config('settings'))) {
            $links['settings'] = glsr(Builder::class)->a([
                'href' => glsr_admin_url('settings', 'addons', $this->addon->slug),
                'text' => _x('Settings', 'admin-text', 'site-reviews'),
            ]);
        }
        if (glsr()->hasPermission('documentation')) {
            $links['documentation'] = glsr(Builder::class)->a([
                'data-expand' => '#addon-'.$this->addon->id,
                'href' => glsr_admin_url('documentation', 'addons'),
                'text' => _x('Help', 'admin-text', 'site-reviews'),
            ]);
        }
        return $links;
    }

    /**
     * @filter site-reviews/capabilities
     */
    public function filterCapabilities(array $capabilities): array
    {
        if (!$this->addon->post_type) { // @phpstan-ignore-line
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
            $capabilities[] = str_replace('post', $this->addon->post_type, $capability);
        }
        return $capabilities;
    }

    /**
     * @param string $path
     * @return string
     * @filter site-reviews/config
     */
    public function filterConfigPath($path)
    {
        $addonPrefix = $this->addon->id.'/';
        return Str::contains($path, $addonPrefix)
            ? $addonPrefix.str_replace($addonPrefix, '', $path)
            : $path;
    }

    /**
     * @return array
     * @filter site-reviews/addon/documentation
     */
    public function filterDocumentation(array $documentation)
    {
        $notice = glsr()->build('views/partials/addons/support-notice', [
            'addon_id' => $this->addon->id,
        ]);
        $support = $this->addon->build('views/documentation');
        $documentation[$this->addon->id] = $notice.$support;
        return $documentation;
    }

    /**
     * @param string $path
     * @param string $file
     * @return string
     * @filter site-reviews/path
     */
    public function filterFilePaths($path, $file)
    {
        $addonPrefix = $this->addon->id.'/';
        return Str::startsWith($file, $addonPrefix)
            ? $this->addon->path(Str::replaceFirst($addonPrefix, '', $file))
            : $path;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter gettext_{addon_id}
     */
    public function filterGettext($translation, $text)
    {
        return glsr(Translator::class)->translate($translation, $this->addon->id, [
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $text
     * @param string $context
     * @return string
     * @filter gettext_with_context_{addon_id}
     */
    public function filterGettextWithContext($translation, $text, $context)
    {
        return glsr(Translator::class)->translate($translation, $this->addon->id, [
            'context' => $context,
            'single' => $text,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @return string
     * @filter ngettext_{addon_id}
     */
    public function filterNgettext($translation, $single, $plural, $number)
    {
        return glsr(Translator::class)->translate($translation, $this->addon->id, [
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param string $translation
     * @param string $single
     * @param string $plural
     * @param int $number
     * @param string $context
     * @return string
     * @filter ngettext_with_context_{addon_id}
     */
    public function filterNgettextWithContext($translation, $single, $plural, $number, $context)
    {
        return glsr(Translator::class)->translate($translation, $this->addon->id, [
            'context' => $context,
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @param string $view
     * @return string
     * @filter {addon_id}/render/view
     */
    public function filterRenderView($view)
    {
        $style = glsr_get_option('general.style', 'default');
        $styledView = sprintf('views/styles/%s/%s', $style, basename($view));
        if (file_exists($this->addon->file($styledView))) {
            return $styledView;
        }
        return $view;
    }

    /**
     * @filter site-reviews/roles
     */
    public function filterRoles(array $roles): array
    {
        if (!$this->addon->post_type) { // @phpstan-ignore-line
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
                $roles[$role][] = str_replace('post', $this->addon->post_type, $capability);
            }
        }
        return $roles;
    }

    /**
     * @return array
     * @filter site-reviews/defer-scripts
     */
    public function filterScriptsDefer(array $handles)
    {
        return $handles;
    }

    /**
     * @return array
     * @filter site-reviews/addon/settings
     */
    public function filterSettings(array $settings)
    {
        return array_merge($this->addon->config('settings'), $settings);
    }

    /**
     * @return array
     * @filter site-reviews/addon/subsubsub
     */
    public function filterSubsubsub(array $subsubsub)
    {
        return $subsubsub;
    }

    /**
     * @return array
     * @filter site-reviews/translation/entries
     */
    public function filterTranslationEntries(array $entries)
    {
        $potFile = $this->addon->path($this->addon->languages.'/'.$this->addon->id.'.pot');
        return glsr(Translation::class)->extractEntriesFromPotFile($potFile, $this->addon->id, $entries);
    }

    /**
     * @return array
     * @filter site-reviews/translator/domains
     */
    public function filterTranslatorDomains(array $domains)
    {
        $domains[] = $this->addon->id;
        return $domains;
    }

    /**
     * @return void
     * @action {addon_id}/activate
     */
    public function install()
    {
    }

    /**
     * @return void
     * @action admin_init
     */
    public function onActivation()
    {
        $activatedOption = glsr()->prefix.'activated_'.$this->addon->id;
        if (empty(get_option($activatedOption))) {
            $this->addon->action('activate');
            update_option($activatedOption, true);
        }
    }

    /**
     * @return void
     * @action init
     */
    public function registerBlocks()
    {
    }

    /**
     * @return void
     * @action init
     */
    public function registerLanguages()
    {
        load_plugin_textdomain($this->addon->id, false,
            trailingslashit(plugin_basename($this->addon->path()).'/'.$this->addon->languages)
        );
    }

    /**
     * @return void
     * @action init
     */
    public function registerShortcodes()
    {
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerTinymcePopups()
    {
    }

    /**
     * @return void
     * @action widgets_init
     */
    public function registerWidgets()
    {
    }

    /**
     * @param string $rows
     * @return void
     * @action site-reviews/addon/settings/{addon_slug}
     */
    public function renderSettings($rows)
    {
        glsr(Template::class)->render($this->addon->id.'/views/settings', [
            'context' => [
                'rows' => $rows,
                'title' => $this->addon->name,
            ],
        ]);
    }

    /**
     * @param string $extension
     * @return array
     */
    protected function buildAssetArgs($extension, array $args = [])
    {
        $args = wp_parse_args($args, [
            'in_footer' => false,
            'suffix' => '',
        ]);
        $dependencies = Arr::get($args, 'dependencies', [glsr()->id.Str::prefix($args['suffix'], '/')]);
        $path = 'assets/'.$this->addon->id.Str::prefix($args['suffix'], '-').'.'.$extension;
        if (!file_exists($this->addon->path($path)) || !in_array($extension, ['css', 'js'])) {
            return [];
        }
        $funcArgs = [
            $this->addon->id.Str::prefix($args['suffix'], '/'),
            $this->addon->url($path),
            Arr::consolidate($dependencies),
            $this->addon->version,
        ];
        if ('js' === $extension && wp_validate_boolean($args['in_footer'])) {
            $funcArgs[] = true; // load script in the footer
        }
        return $funcArgs;
    }

    /**
     * @param string $extension
     * @return void
     */
    protected function enqueueAsset($extension, array $args = [])
    {
        $args = $this->buildAssetArgs($extension, $args);
        if (!empty($args)) {
            $function = 'js' === $extension
                ? 'wp_enqueue_script'
                : 'wp_enqueue_style';
            call_user_func_array($function, $args);
        }
    }

    /**
     * @param string $extension
     * @return void
     */
    protected function registerAsset($extension, array $args = [])
    {
        if ($args = $this->buildAssetArgs($extension, $args)) {
            $function = 'js' === $extension
                ? 'wp_register_script'
                : 'wp_register_style';
            call_user_func_array($function, $args);
        }
    }

    /**
     * @return void
     */
    abstract protected function setAddon();
}
