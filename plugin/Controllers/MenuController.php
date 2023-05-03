<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Defaults\AddonDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Settings;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\SystemInfo;

class MenuController extends Controller
{
    /**
     * @return void
     * @action admin_menu
     */
    public function registerMenuCount()
    {
        global $menu, $typenow;
        foreach ($menu as $key => $value) {
            if (!isset($value[2]) || $value[2] != 'edit.php?post_type='.glsr()->post_type) {
                continue;
            }
            $postCount = wp_count_posts(glsr()->post_type);
            $pendingCount = glsr(Builder::class)->span(number_format_i18n($postCount->pending), [
                'class' => 'unapproved-count',
            ]);
            $awaitingModeration = glsr(Builder::class)->span($pendingCount, [
                'class' => 'awaiting-mod count-'.$postCount->pending,
            ]);
            $menu[$key][0] .= ' '.$awaitingModeration;
            if (glsr()->post_type === $typenow) {
                $menu[$key][4] .= ' current';
            }
            break;
        }
    }

    /**
     * @return void
     * @action admin_menu
     */
    public function registerSubMenus()
    {
        $pages = $this->parseWithFilter('submenu/pages', [
            'settings' => _x('Settings', 'admin-text', 'site-reviews'),
            'tools' => _x('Tools', 'admin-text', 'site-reviews'),
            'addons' => _x('Addons', 'admin-text', 'site-reviews'),
            'documentation' => _x('Help & Support', 'admin-text', 'site-reviews'),
        ]);
        foreach ($pages as $slug => $title) {
            $method = Helper::buildMethodName('render-'.$slug.'-menu');
            if (!method_exists($this, $method)) {
                continue;
            }
            $callback = glsr()->filter('addon/submenu/callback', [$this, $method], $slug);
            if (!is_callable($callback)) {
                continue;
            }
            add_submenu_page('edit.php?post_type='.glsr()->post_type, $title, $title, glsr()->getPermission($slug), Str::dashCase(glsr()->prefix).$slug, $callback);
        }
    }

    /**
     * We don't use admin_menu because it breaks the privilege check which runs
     * after the admin_menu hook is triggered in wp-admin/includes/menu.php.
     * @return void
     * @action admin_init
     */
    public function removeSubMenu()
    {
        if (!function_exists('remove_submenu_page')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }
        remove_submenu_page(
            'edit.php?post_type='.glsr()->post_type,
            'post-new.php?post_type='.glsr()->post_type
        );
    }

    /**
     * @return void
     * @see $this->registerSubMenus()
     * @callback add_submenu_page
     */
    public function renderAddonsMenu()
    {
        $addons = [];
        $data = glsr(Api::class)->get('addons')->data();
        foreach ($data as $values) {
            $context = glsr(AddonDefaults::class)->restrict($values);
            $addons[] = array_merge($context, compact('context'));
        }
        $this->renderPage('addons', [
            'addons' => $addons,
            'template' => glsr(Template::class),
        ]);
    }

    /**
     * @return void
     * @see $this->registerSubMenus()
     * @callback add_submenu_page
     */
    public function renderDocumentationMenu()
    {
        $tabs = $this->parseWithFilter('documentation/tabs', [
            'support' => _x('Support', 'admin-text', 'site-reviews'),
            'faq' => _x('FAQ', 'admin-text', 'site-reviews'),
            'shortcodes' => _x('Shortcodes', 'admin-text', 'site-reviews'),
            'hooks' => _x('Hooks', 'admin-text', 'site-reviews'),
            'functions' => _x('Functions', 'admin-text', 'site-reviews'),
            'api' => _x('API', 'admin-text', 'site-reviews'),
            'addons' => _x('Addons', 'admin-text', 'site-reviews'),
        ]);
        $addons = glsr()->filterArray('addon/documentation', []);
        uksort($addons, function ($a, $b) {
            return strnatcasecmp(glsr($a)->name, glsr($b)->name);
        });
        if (empty($addons)) {
            unset($tabs['addons']);
        }
        $this->renderPage('documentation', [
            'addons' => $addons,
            'tabs' => $tabs,
        ]);
    }

    /**
     * @return void
     * @see $this->registerSubMenus()
     * @callback add_submenu_page
     */
    public function renderSettingsMenu()
    {
        $tabs = $this->parseWithFilter('settings/tabs', [ // order is intentional
            'general' => _x('General', 'admin-text', 'site-reviews'),
            'reviews' => _x('Reviews', 'admin-text', 'site-reviews'),
            'forms' => _x('Forms', 'admin-text', 'site-reviews'),
            'schema' => _x('Schema', 'admin-text', 'site-reviews'),
            'strings' => _x('Strings', 'admin-text', 'site-reviews'),
            'addons' => _x('Addons', 'admin-text', 'site-reviews'),
            'licenses' => _x('Licenses', 'admin-text', 'site-reviews'),
        ]);
        if (empty(Arr::get(glsr()->defaults, 'settings.addons'))) {
            unset($tabs['addons']);
        }
        if (empty(Arr::get(glsr()->defaults, 'settings.licenses'))) {
            unset($tabs['licenses']);
        }
        $this->renderPage('settings', [
            'settings' => glsr(Settings::class),
            'tabs' => $tabs,
        ]);
    }

    /**
     * @return void
     * @see $this->registerSubMenus()
     * @callback add_submenu_page
     */
    public function renderToolsMenu()
    {
        $tabs = $this->parseWithFilter('tools/tabs', [
            'general' => _x('General', 'admin-text', 'site-reviews'),
            'scheduled' => _x('Scheduled Actions', 'admin-text', 'site-reviews'),
            'sync' => _x('Sync Reviews', 'admin-text', 'site-reviews'),
            'console' => _x('Console', 'admin-text', 'site-reviews'),
            'system-info' => _x('System Info', 'admin-text', 'site-reviews'),
        ]);
        if (!glsr()->filterBool('addon/sync/enable', false)) {
            unset($tabs['sync']);
        }
        $this->renderPage('tools', [
            'data' => [
                'console_level' => glsr(Console::class)->getLevel(),
                'context' => [
                    'base_url' => glsr_admin_url(),
                    'console' => glsr(Console::class)->get(),
                    'id' => glsr()->id,
                ],
                'myisam_tables' => Arr::get(glsr(Tables::class)->tableEngines(), 'MyISAM', []),
                'rollback_script' => file_get_contents(glsr()->path('assets/scripts/rollback.js')),
                'rollback_versions' => glsr(Cache::class)->getPluginVersions(),
                'services' => glsr()->filterArray('addon/sync/services', []),
            ],
            'tabs' => $tabs,
            'template' => glsr(Template::class),
        ]);
    }

    /**
     * @return void
     * @action admin_init
     */
    public function setCustomPermissions()
    {
        foreach (wp_roles()->roles as $role => $value) {
            wp_roles()->remove_cap($role, 'create_'.glsr()->post_type);
        }
    }

    /**
     * @return string
     */
    protected function getNotices()
    {
        return glsr(Builder::class)->div(glsr(Notice::class)->get(), [
            'id' => 'glsr-notices',
        ]);
    }

    /**
     * @param string $hookSuffix
     * @return array
     */
    protected function parseWithFilter($hookSuffix, array $args = [])
    {
        if (Str::endsWith($hookSuffix, '/tabs')) {
            $page = str_replace('/tabs', '', $hookSuffix);
            foreach ($args as $tab => $title) {
                if (!glsr()->hasPermission($page, $tab)) {
                    unset($args[$tab]);
                }
            }
        }
        return glsr()->filterArray('addon/'.$hookSuffix, $args);
    }

    /**
     * @param string $page
     * @return void
     */
    protected function renderPage($page, array $data = [])
    {
        $data['http_referer'] = (string) wp_get_referer();
        $data['notices'] = $this->getNotices();
        glsr()->render('pages/'.$page.'/index', $data);
    }
}
