<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Defaults\TutorialDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class WelcomeController extends Controller
{
    protected $welcomePage;

    public function __construct()
    {
        $this->welcomePage = glsr()->id.'-welcome';
    }

    /**
     * @return array
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links)
    {
        $links['welcome'] = glsr(Builder::class)->a([
            'href' => esc_url(glsr_admin_url('welcome')),
            'text' => _x('About', 'admin-text', 'site-reviews'),
        ]);
        return $links;
    }

    /**
     * @return string
     * @filter admin_title
     */
    public function filterAdminTitle($title)
    {
        return 'dashboard_page_'.$this->welcomePage === glsr_current_screen()->id
            ? sprintf(_x('Welcome to %s &#8212; WordPress', 'admin-text', 'site-reviews'), glsr()->name)
            : $title;
    }

    /**
     * @return void
     * @action admin_menu
     */
    public function registerPage()
    {
        add_dashboard_page(
            sprintf(_x('Welcome to %s', 'admin-text', 'site-reviews'), glsr()->name),
            glsr()->name,
            glsr()->getPermission('welcome'),
            $this->welcomePage,
            [$this, 'renderPage']
        );
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
        remove_submenu_page('index.php', $this->welcomePage);
    }

    /**
     * @return void
     * @see $this->registerPage()
     * @callback add_dashboard_page
     */
    public function renderPage()
    {
        $data = glsr(Api::class)->get('tutorials')->data();
        $data = glsr(TutorialDefaults::class)->restrict($data);
        $tabs = glsr()->filterArray('addon/welcome/tabs', [
            'getting-started' => _x('Getting Started', 'admin-text', 'site-reviews'),
            'whatsnew' => _x('What\'s New', 'admin-text', 'site-reviews'),
            'upgrade-guide' => _x('Upgrade Guide', 'admin-text', 'site-reviews'),
            'support' => _x('Support', 'admin-text', 'site-reviews'),
        ]);
        glsr()->render('pages/welcome/index', [
            'data' => $data,
            'http_referer' => (string) wp_get_referer(),
            'tabs' => $tabs,
            'template' => glsr(Template::class),
        ]);
    }
}
