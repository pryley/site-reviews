<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;

class Controller extends AbstractController
{
    /**
     * @action ux_builder_enqueue_scripts
     */
    public function printInlineScripts(string $page = ''): void
    {
        if ('editor' === $page) {
            $script = file_get_contents(glsr()->path('assets/scripts/integrations/flatsome-inline.js'));
            wp_add_inline_script('ux-builder-core', $script);
        }
    }

    /**
     * @action ux_builder_enqueue_scripts
     */
    public function printInlineStyles(string $page = ''): void
    {
        if ('editor' === $page) {
            $css = file_get_contents(glsr()->path('assets/styles/integrations/flatsome-inline.css'));
            wp_add_inline_style('ux-builder-core', $css);
        }
    }

    /**
     * @action init
     */
    public function registerShortcodes(): void
    {
        glsr(FlatsomeSiteReview::class)->register();
        glsr(FlatsomeSiteReviews::class)->register();
        glsr(FlatsomeSiteReviewsForm::class)->register();
        glsr(FlatsomeSiteReviewsSummary::class)->register();
    }

    /**
     * @action wp_ajax_ux_builder_search_posts:1
     */
    public function searchAssignedPosts(): void
    {
        if ('assigned_posts_query' !== filter_input(INPUT_GET, 'option')) {
            return;
        }
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $query = sanitize_text_field((string) filter_input(INPUT_GET, 'query'));
        $args = [
            'post__in' => [],
            'posts_per_page' => 25,
        ];
        if (is_numeric($query)) {
            $args['post__in'][] = (int) $query;
        } else {
            $args['s'] = $query;
        }
        $posts = glsr(Database::class)->posts($args);
        $callback = fn ($id, $title) => compact('id', 'title');
        $items = array_map($callback, array_keys($posts), array_values($posts));
        array_unshift($items,
            [
                'id' => 'post_id',
                'title' => _x('The Current Page', 'admin-text', 'site-reviews'),
            ],
            [
                'id' => 'parent_id',
                'title' => _x('The Parent Page', 'admin-text', 'site-reviews'),
            ]
        );
        wp_send_json_success($items);
    }

    /**
     * @action wp_ajax_ux_builder_search_posts:2
     */
    public function searchAssignedUsers(): void
    {
        if ('assigned_users_query' !== filter_input(INPUT_GET, 'option')) {
            return;
        }
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $query = sanitize_text_field((string) filter_input(INPUT_GET, 'query'));
        $users = glsr(Database::class)->users([
            'number' => 25,
            'search_wild' => $query,
        ]);
        $callback = fn ($id, $title) => compact('id', 'title');
        $items = array_map($callback, array_keys($users), array_values($users));
        array_unshift($items,
            [
                'id' => 'user_id',
                'title' => _x('The Logged-in user', 'admin-text', 'site-reviews'),
            ],
            [
                'id' => 'author_id',
                'title' => _x('The Page author', 'admin-text', 'site-reviews'),
            ],
            [
                'id' => 'profile_id',
                'title' => _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'),
            ]
        );
        wp_send_json_success($items);
    }
}
