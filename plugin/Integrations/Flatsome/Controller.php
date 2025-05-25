<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Controller extends AbstractController
{
    /**
     * @action wp_ajax_ux_builder_get_posts:1
     */
    public function interceptGetPostsQuery(): void
    {
        $option = filter_input(INPUT_GET, 'option');
        if (!str_starts_with($option, glsr()->prefix)) {
            return;
        }
        $option = Str::removePrefix($option, glsr()->prefix);
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $values = filter_input(INPUT_GET, 'values', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($values)) {
            $values = [filter_input(INPUT_GET, 'values')];
        }
        $items = call_user_func([glsr(ShortcodeOptionManager::class), $option], [
            'include' => array_filter($values),
        ]);
        $items = array_filter($items, fn ($id) => in_array($id, $values), \ARRAY_FILTER_USE_KEY);
        $callback = fn ($id, $title) => compact('id', 'title');
        $results = array_map($callback, array_keys($items), array_values($items));
        wp_send_json_success($results);
    }

    /**
     * @action wp_ajax_ux_builder_search_posts:1
     */
    public function interceptSearchPostsQuery(): void
    {
        $option = filter_input(INPUT_GET, 'option');
        if (!str_starts_with($option, glsr()->prefix)) {
            return;
        }
        $option = Str::removePrefix($option, glsr()->prefix);
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $query = filter_input(INPUT_GET, 'query');
        $items = call_user_func([glsr(ShortcodeOptionManager::class), $option], [
            'search' => $query,
        ]);
        $callback = fn ($id, $title) => compact('id', 'title');
        $results = array_map($callback, array_keys($items), array_values($items));
        wp_send_json_success($results);
    }

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
        } else {
            // This fixes JS errors on mouseover events
            wp_add_inline_style('ux-builder-core', '.uxb-draggable>.glsr{pointer-events:none;}');
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
}
