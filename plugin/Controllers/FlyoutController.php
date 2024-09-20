<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Defaults\FlyoutItemDefaults;
use GeminiLabs\SiteReviews\License;

class FlyoutController extends AbstractController
{
    /**
     * @action admin_footer
     */
    public function renderFlyout(): void
    {
        $screen = glsr_current_screen();
        if ('post' === $screen->base) {
            return;
        }
        if (!str_starts_with($screen->post_type, glsr()->post_type)) {
            return;
        }
        if (!glsr()->filterBool('flyoutmenu/enabled', true)) {
            return;
        }
        glsr()->render('views/partials/flyoutmenu', [
            'items' => $this->menuItems(),
        ]);
    }

    protected function menuItems(): array
    {
        $items = [
            [
                'icon' => 'dashicons-star-filled',
                'title' => _x('Upgrade to Premium', 'admin-text', 'site-reviews'),
                'url' => 'https://niftyplugins.com/plugins/site-reviews-premium/',
            ],
            [
                'icon' => 'dashicons-editor-help',
                'title' => _x('Learn the Basics', 'admin-text', 'site-reviews'),
                'url' => glsr_admin_url('welcome'),
            ],
            [
                'icon' => 'dashicons-youtube',
                'title' => _x('Watch a Tutorial', 'admin-text', 'site-reviews'),
                'url' => 'https://youtu.be/H5HdMCXvuq8',
            ],
            [
                'icon' => 'dashicons-sos',
                'title' => _x('Read the Documentation', 'admin-text', 'site-reviews'),
                'url' => glsr_admin_url('documentation'),
            ],
            [
                'icon' => 'dashicons-wordpress-alt',
                'title' => _x('Ask for Help', 'admin-text', 'site-reviews'),
                'url' => 'https://wordpress.org/support/plugin/site-reviews/',
            ],
        ];
        if (glsr(License::class)->isPremium()) {
            array_shift($items);
        }
        $items = glsr()->filterArray('flyoutmenu/items', $items);
        array_walk($items, function (&$item) {
            $item = glsr(FlyoutItemDefaults::class)->restrict($item);
        });
        return $items;
    }
}
