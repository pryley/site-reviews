<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class WelcomeController extends Controller
{
    /**
     * @return array
     * @filter plugin_action_links_site-reviews/site-reviews.php
     */
    public function filterActionLinks(array $links)
    {
        $links['welcome'] = glsr(Builder::class)->a(__('About', 'site-reviews'), [
            'href' => admin_url('edit.php?post_type='.Application::POST_TYPE.'&page=welcome'),
        ]);
        return $links;
    }

    /**
     * @return string
     * @filter admin_title
     */
    public function filterAdminTitle($title)
    {
        return Application::POST_TYPE.'_page_welcome' == glsr_current_screen()->id
            ? sprintf(__('Welcome to %s &#8212; WordPress', 'site-reviews'), glsr()->name)
            : $title;
    }

    /**
     * @param string $text
     * @return string
     * @filter admin_footer_text
     */
    public function filterFooterText($text)
    {
        if (Application::POST_TYPE.'_page_welcome' != glsr_current_screen()->id) {
            return $text;
        }
        $url = 'https://wordpress.org/support/view/plugin-reviews/site-reviews?filter=5#new-post';
        return wp_kses_post(sprintf(
            __('Please rate %s on %s and help us spread the word. Thank you so much!', 'site-reviews'),
            '<strong>'.glsr()->name.'</strong> <a href="'.$url.'" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
            '<a href="'.$url.'" target="_blank">wordpress.org</a>'
        ));
    }

    /**
     * @param string $plugin
     * @param bool $isNetworkActivation
     * @return void
     * @action activated_plugin
     */
    public function redirectOnActivation($plugin, $isNetworkActivation)
    {
        if (!$isNetworkActivation
            && 'cli' !== php_sapi_name()
            && $plugin === plugin_basename(glsr()->file)) {
            wp_safe_redirect(admin_url('edit.php?post_type='.Application::POST_TYPE.'&page=welcome'));
            exit;
        }
    }

    /**
     * @return void
     * @action admin_menu
     */
    public function registerPage()
    {
        add_submenu_page('edit.php?post_type='.Application::POST_TYPE,
            sprintf(__('Welcome to %s', 'site-reviews'), glsr()->name),
            glsr()->name,
            glsr()->getPermission('welcome'),
            'welcome',
            [$this, 'renderPage']
        );
        remove_submenu_page('edit.php?post_type='.Application::POST_TYPE, 'welcome');
    }

    /**
     * @return void
     * @see $this->registerPage()
     * @callback add_submenu_page
     */
    public function renderPage()
    {
        $tabs = apply_filters('site-reviews/addon/welcome/tabs', [
            'getting-started' => __('Getting Started', 'site-reviews'),
            'whatsnew' => __('What\'s New', 'site-reviews'),
            'upgrade-guide' => __('Upgrade Guide', 'site-reviews'),
            'support' => __('Support', 'site-reviews'),
        ]);
        glsr()->render('pages/welcome/index', [
            'data' => ['context' => []],
            'http_referer' => (string) wp_get_referer(),
            'tabs' => $tabs,
            'template' => glsr(Template::class),
        ]);
    }
}
