<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePage;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @action admin_action_dt_duplicate_post_as_draft
     */
    public function duplicateReview(): void
    {
        $nonce = filter_input(INPUT_GET, 'nonce') ?: filter_input(INPUT_POST, 'nonce');
        $postId = filter_input(INPUT_GET, 'post') ?: filter_input(INPUT_POST, 'post');
        if (!Review::isReview($postId)) {
            return; // not a review so don't override
        }
        if (!wp_verify_nonce(sanitize_text_field($nonce), "dt-duplicate-page-{$postId}")) {
            return; // let the integrated plugin handle errors
        }
        if (!glsr()->can('edit_posts')) {
            wp_die(_x('Unauthorized Access.', 'admin-text', 'site-reviews'));
        }
        if (!$review = glsr(ReviewManager::class)->duplicate((int) $postId)) {
            wp_die(_x('Invalid review.', 'admin-text', 'site-reviews'));
        }
        $options = get_option('duplicate_page_options');
        if (!empty($options['duplicate_post_suffix'])) {
            wp_update_post([
                'ID' => $review->ID,
                'post_title' => Str::suffix($review->title, ' -- '.$options['duplicate_post_suffix']),
            ]);
        }
        wp_redirect($this->redirectUrl($review->ID));
        exit;
    }

    protected function redirectUrl(int $postId): string
    {
        $options = get_option('duplicate_page_options');
        $redirect = $options['duplicate_post_redirect'] ?? 'to_list';
        if ('to_page' === $redirect) {
            $url = admin_url('edit.php?post_type='.glsr()->post_type);
        } else {
            $url = admin_url('post.php?action=edit&post='.$postId);
        }
        return sanitize_url($url);
    }
}
