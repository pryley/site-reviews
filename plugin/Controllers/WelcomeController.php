<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Defaults\TutorialDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class WelcomeController extends AbstractController
{
    protected $welcomePage;

    public function __construct()
    {
        $this->welcomePage = glsr()->id.'-welcome';
    }

    /**
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links): array
    {
        $links['welcome'] = glsr(Builder::class)->a([
            'href' => esc_url(glsr_admin_url('welcome')),
            'text' => _x('About', 'admin-text', 'site-reviews'),
        ]);
        return $links;
    }

    /**
     * @filter admin_title
     */
    public function filterAdminTitle(string $title): string
    {
        return "dashboard_page_{$this->welcomePage}" === glsr_current_screen()->id
            ? sprintf(_x('Welcome to %s &#8212; WordPress', 'admin-text', 'site-reviews'), glsr()->name)
            : $title;
    }

    /**
     * @action admin_menu
     */
    public function registerPage(): void
    {
        add_dashboard_page(
            sprintf(_x('Welcome to %s', 'admin-text', 'site-reviews'), glsr()->name),
            glsr()->name,
            glsr()->getPermission('welcome'),
            $this->welcomePage,
            [$this, 'renderPageCallback']
        );
    }

    /**
     * We don't use admin_menu because it breaks the privilege check which runs
     * after the admin_menu hook is triggered in wp-admin/includes/menu.php.
     *
     * @action admin_init
     */
    public function removeSubMenu(): void
    {
        if (!function_exists('remove_submenu_page')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }
        remove_submenu_page('index.php', $this->welcomePage);
    }

    /**
     * @see registerPage
     */
    public function renderPageCallback(): void
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
        ]);
    }
}
