<?php

namespace GeminiLabs\SiteReviews\Addons;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Str;
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
        if (!$this->isReviewAdminPage()) {
            return;
        }
        $this->enqueueAsset('css', ['suffix' => 'admin']);
        $this->enqueueAsset('js', ['suffix' => 'admin']);
    }

    /**
     * @return void
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockAssets()
    {
        $this->registerAsset('css', ['suffix' => 'blocks']);
        $this->registerAsset('js', ['suffix' => 'blocks']);
    }

    /**
     * @return void
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicAssets()
    {
        $this->enqueueAsset('css');
        $this->enqueueAsset('js');
    }

    /**
     * @return array
     * @filter plugin_action_links_{addon_id}/{addon_id}.php
     */
    public function filterActionLinks(array $links)
    {
        $links['settings'] = glsr(Builder::class)->a(__('Settings', 'site-reviews-images'), [
            'href' => admin_url('edit.php?post_type='.glsr()->post_type.'&page=settings#tab-addons'),
        ]);
        return $links;
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
        $documentation[$this->addon->name] = glsr(Template::class)->build($this->addon->id.'/documentation');
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
        return Str::startsWith($addonPrefix, $file)
            ? $this->addon->path(Str::replaceFirst($addonPrefix, '', $file))
            : $path;
    }

    /**
     * @param string $translation
     * @param string $text
     * @return string
     * @filter site-reviews/gettext/{addon_id}
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
     * @filter site-reviews/gettext_with_context/{addon_id}
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
     * @filter site-reviews/ngettext/{addon_id}
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
     * @filter site-reviews/ngettext_with_context/{addon_id}
     */
    public function filterNgettextWithContextSiteReviews($translation, $single, $plural, $number, $context)
    {
        return glsr(Translator::class)->translate($translation, $this->addon->id, [
            'context' => $context,
            'number' => $number,
            'plural' => $plural,
            'single' => $single,
        ]);
    }

    /**
     * @return array
     * @filter site-reviews/addon/settings
     */
    public function filterSettings(array $settings)
    {
        $settingsFile = $this->addon->path('config/settings.php');
        if (file_exists($settingsFile)) {
            $settings = array_merge((include $settingsFile), $settings);
        }
        return $settings;
    }

    /**
     * @return array
     * @filter site-reviews/addon/system-info
     */
    public function filterSystemInfo(array $details)
    {
        $version = $this->addon->version;
        $previousVersion = glsr(OptionManager::class)->get('addons.'.$this->addon->id.'.version_upgraded_from');
        if (empty($previousVersion)) {
            $previousVersion = $version;
        }
        $details[$this->addon->name] = sprintf('%s (%s)', $this->addon->version, $previousVersion);
        return $details;
    }

    /**
     * @return array
     * @filter site-reviews/translation/entries
     */
    public function filterTranslationEntries(array $entries)
    {
        $potFile = $this->addon->path($this->addon->languages.'/'.$this->addon->id.'.pot');
        return glsr(Translation::class)->extractEntriesFromPotFile($potFile, $entries);
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
     * @action init
     */
    public function registerBlocks()
    {
    }

    /**
     * @return void
     * @action plugins_loaded
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
            'dependencies' => [],
            'in_footer' => true,
            'suffix' => '',
        ]);
        $path = 'assets/'.$this->addon->id.Str::prefix('-', $args['suffix']).'.'.$extension;
        if (!file_exists($this->addon->path($path)) || !in_array($extension, ['css', 'js'])) {
            return [];
        }
        $funcArgs = [
            $this->addon->id.Str::prefix('/', $args['suffix']),
            $this->addon->url($path),
            array_merge([glsr()->id.Str::prefix('/', $args['suffix'])], $args['dependencies']),
            $this->addon->version,
        ];
        if ('js' === $extension && $args['in_footer']) {
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
        if ($args = $this->buildAssetArgs($extension, $args)) {
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
