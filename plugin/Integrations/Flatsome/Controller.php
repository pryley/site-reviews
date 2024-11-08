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
        if ('editor' !== $page) {
            return;
        }
        $script = 'var glsr_ux_builder=t=>{if(t.tag.startsWith("ux_site_review")){var r=t.$scope.$ctrl.targets.$iframe().get(0);let e=r.contentWindow||r;e.GLSR.Event.trigger("site-reviews/init"),t.$element.find(":input,a").attr("tabindex",-1).css({"pointer-events":"none"})}};UxBuilder.on("shortcode-attached",glsr_ux_builder);';
        wp_add_inline_script('ux-builder-core', $script);
    }

    /**
     * @action ux_builder_enqueue_scripts
     */
    public function printInlineStyles(string $page = ''): void
    {
        if ('editor' !== $page) {
            return;
        }
        $css = '';
        $css .= '.add-shortcode-items ul .add-shortcode-box .add-shortcode-box-button:has(img[src*="/icons/flatsome/flatsome-"]){display:flex;flex-wrap:wrap;justify-content:center;}';
        $css .= '.add-shortcode-items ul .add-shortcode-box img[src*="/icons/flatsome/flatsome-"]{height:36px;margin-top:13px;}';
        $css .= 'ux-option[class*="-glsr_group_"] ux-options ux-option.option-checkbox:not(.is-full-width,:has(.option-description)){.option-header{max-width:unset;}.option-body{flex:0;}.option-template{min-width:unset;}}';
        wp_add_inline_style('ux-builder-core', $css);
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
        $posts = glsr(Database::class)->posts([
            'posts_per_page' => 25,
            's' => $query,
        ]);
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
            'search' => $query,
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
