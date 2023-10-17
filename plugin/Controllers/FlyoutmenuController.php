<?php

namespace GeminiLabs\SiteReviews\Controllers;

class FlyoutmenuController extends Controller
{
    /**
     * @action admin_footer
     */
    public function renderFlyoutmenu(): void
    {
        if (!$this->isReviewAdminScreen()) {
            return;
        }
        if (!glsr()->filterBool('enable/flyoutmenu', true)) {
            return;
        }
        glsr()->render('views/partials/flyoutmenu', [
            'items' => $this->menuItems(),
        ]);
    }

    protected function menuItems(): array
    {
        $is_pro = wpforms()->is_pro();
        $utm_campaign = $is_pro ? 'plugin' : 'liteplugin';
        $items = [
            [
                'title' => esc_html_x('Ask for Help', 'admin-text', 'site-reviews'),
                'url' => 'https://wordpress.org/support/plugin/site-reviews/',
                'icon' => 'dashicons-wordpress-alt',
            ],
            [
                'title' => esc_html_x('Documentation', 'admin-text', 'site-reviews'),
                'url' => glsr_admin_url('documentation'),
                'icon' => 'dashicons-sos',
            ],
            [
                'title' => esc_html_x('How to Use', 'admin-text', 'site-reviews'),
                'url' => glsr_admin_url('welcome'),
                'icon' => 'dashicons-editor-help',
            ],
            [
                'title' => esc_html_x('Upgrade to Site Reviews Premium', 'admin-text', 'site-reviews'),
                'url' => 'https://niftyplugins.com/plugins/site-reviews-premium/',
                'icon' => 'dashicons-star-filled',
                'bgcolor' => '#E1772F',
                'hover_bgcolor' => '#ff8931',
            ],
        ];
        if ($is_pro) {
            array_shift($items);
        }
        return $items;
    }
}
