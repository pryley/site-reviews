<?php

namespace GeminiLabs\SiteReviews\Controllers;

class NetworkController extends AbstractController
{
    /**
     * @action admin_bar_menu:30
     */
    public function extendAdminBar(\WP_Admin_Bar $wp_admin_bar): void
    {
        if (!is_user_logged_in() || !is_multisite()) {
            return;
        }
        if (count($wp_admin_bar->user->blogs) < 1 && !current_user_can('manage_network')) {
            return;
        }
        foreach ((array) $wp_admin_bar->user->blogs as $blog) {
            switch_to_blog($blog->userblog_id);
            if (glsr()->can('edit_posts')) {
                $menuId = "blog-{$blog->userblog_id}";
                $wp_admin_bar->add_node([
                    'href' => glsr_admin_url(),
                    'id' => "{$menuId}-site-reviews",
                    'parent' => $menuId,
                    'title' => _x('Manage Reviews', 'admin-text', 'site-reviews'),
                ]);
                if ($visitSiteNode = $wp_admin_bar->get_node("{$menuId}-v")) {
                    $wp_admin_bar->remove_node("{$menuId}-v");
                    $wp_admin_bar->add_node((array) $visitSiteNode);
                }
            }
            restore_current_blog();
        }
    }
}
