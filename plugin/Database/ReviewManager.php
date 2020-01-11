<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsSummaryDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use WP_Post;
use WP_Query;

class ReviewManager
{
    /**
     * @return false|Review
     */
    public function create(CreateReview $command)
    {
        $reviewValues = glsr(CreateReviewDefaults::class)->restrict((array) $command);
        $reviewValues = apply_filters('site-reviews/create/review-values', $reviewValues, $command);
        $reviewValues = Arr::prefixArrayKeys($reviewValues);
        unset($reviewValues['json']); // @todo remove the need for this
        $postValues = [
            'comment_status' => 'closed',
            'meta_input' => $reviewValues,
            'ping_status' => 'closed',
            'post_content' => $reviewValues['_content'],
            'post_date' => $reviewValues['_date'],
            'post_date_gmt' => get_gmt_from_date($reviewValues['_date']),
            'post_name' => $reviewValues['_review_type'].'-'.$reviewValues['_review_id'],
            'post_status' => $this->getNewPostStatus($reviewValues, $command->blacklisted),
            'post_title' => $reviewValues['_title'],
            'post_type' => Application::POST_TYPE,
        ];
        $postId = wp_insert_post($postValues, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($postValues);
            return false;
        }
        $this->setTerms($postId, $command->category);
        $review = $this->single(get_post($postId));
        do_action('site-reviews/review/created', $review, $command);
        return $review;
    }

    /**
     * @param string $metaReviewId
     * @return void
     */
    public function delete($metaReviewId)
    {
        if ($postId = $this->getPostId($metaReviewId)) {
            wp_delete_post($postId, true);
        }
    }

    /**
     * @return object
     */
    public function get(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->merge($args);
        $metaQuery = glsr(QueryBuilder::class)->buildQuery(
            ['assigned_to', 'email', 'ip_address', 'type', 'rating'],
            $args
        );
        $taxQuery = glsr(QueryBuilder::class)->buildQuery(
            ['category'],
            ['category' => $this->normalizeTermIds($args['category'])]
        );
        $paged = glsr(QueryBuilder::class)->getPaged(
            wp_validate_boolean($args['pagination'])
        );
        $parameters = [
            'meta_key' => '_pinned',
            'meta_query' => $metaQuery,
            'offset' => $args['offset'],
            'order' => $args['order'],
            'orderby' => 'meta_value '.$args['orderby'],
            'paged' => Arr::get($args, 'paged', $paged),
            'post__in' => $args['post__in'],
            'post__not_in' => $args['post__not_in'],
            'post_status' => 'publish',
            'post_type' => Application::POST_TYPE,
            'posts_per_page' => $args['per_page'],
            'tax_query' => $taxQuery,
        ];
        $parameters = apply_filters('site-reviews/get/reviews/query', $parameters, $args);
        $query = new WP_Query($parameters);
        $results = array_map([$this, 'single'], $query->posts);
        $reviews = new Reviews($results, $query->max_num_pages, $args);
        return apply_filters('site-reviews/get/reviews', $reviews, $query);
    }

    /**
     * @param string $metaReviewId
     * @return int
     */
    public function getPostId($metaReviewId)
    {
        return glsr(SqlQueries::class)->getPostIdFromReviewId($metaReviewId);
    }

    /**
     * @return array
     */
    public function getRatingCounts(array $args = [])
    {
        $args = glsr(SiteReviewsSummaryDefaults::class)->filter($args);
        $counts = glsr(CountsManager::class)->getCounts([
            'post_ids' => Arr::convertStringToArray($args['assigned_to']),
            'term_ids' => $this->normalizeTermIds($args['category']),
            'type' => $args['type'],
        ]);
        return glsr(CountsManager::class)->flatten($counts, [
            'min' => $args['rating'],
        ]);
    }

    /**
     * @param string $commaSeparatedTermIds
     * @return array
     */
    public function normalizeTermIds($commaSeparatedTermIds)
    {
        $termIds = glsr_array_column($this->normalizeTerms($commaSeparatedTermIds), 'term_id');
        return array_unique(array_map('intval', $termIds));
    }

    /**
     * @param string $commaSeparatedTermIds
     * @return array
     */
    public function normalizeTerms($commaSeparatedTermIds)
    {
        $terms = [];
        $termIds = Arr::convertStringToArray($commaSeparatedTermIds);
        foreach ($termIds as $termId) {
            if (is_numeric($termId)) {
                $termId = intval($termId);
            }
            $term = term_exists($termId, Application::TAXONOMY);
            if (!isset($term['term_id'])) {
                continue;
            }
            $terms[] = $term;
        }
        return $terms;
    }

    /**
     * @param int $postId
     * @return void
     */
    public function revert($postId)
    {
        if (Application::POST_TYPE != get_post_field('post_type', $postId)) {
            return;
        }
        delete_post_meta($postId, '_edit_last');
        $result = wp_update_post([
            'ID' => $postId,
            'post_content' => glsr(Database::class)->get($postId, 'content'),
            'post_date' => glsr(Database::class)->get($postId, 'date'),
            'post_title' => glsr(Database::class)->get($postId, 'title'),
        ]);
        if (is_wp_error($result)) {
            glsr_log()->error($result->get_error_message());
            return;
        }
        do_action('site-reviews/review/reverted', glsr_get_review($postId));
    }

    /**
     * @return Review
     */
    public function single(WP_Post $post)
    {
        if (Application::POST_TYPE != $post->post_type) {
            $post = new WP_Post((object) []);
        }
        $review = new Review($post);
        return apply_filters('site-reviews/get/review', $review, $post);
    }

    /**
     * @param bool $isBlacklisted
     * @return string
     */
    protected function getNewPostStatus(array $reviewValues, $isBlacklisted)
    {
        $requireApproval = glsr(OptionManager::class)->getBool('settings.general.require.approval');
        return 'local' == $reviewValues['_review_type'] && ($requireApproval || $isBlacklisted)
            ? 'pending'
            : 'publish';
    }

    /**
     * @param int $postId
     * @param string $termIds
     * @return void
     */
    protected function setTerms($postId, $termIds)
    {
        $termIds = $this->normalizeTermIds($termIds);
        if (empty($termIds)) {
            return;
        }
        $termTaxonomyIds = wp_set_object_terms($postId, $termIds, Application::TAXONOMY);
        if (is_wp_error($termTaxonomyIds)) {
            glsr_log()->error($termTaxonomyIds->get_error_message());
        }
    }
}
