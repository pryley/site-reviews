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
        $reviewValues = glsr()->filterArray('create/review-values', $reviewValues, $command);
        $reviewValues = Arr::prefixKeys($reviewValues);
        $postValues = [
            'comment_status' => 'closed',
            'meta_input' => $reviewValues,
            'ping_status' => 'closed',
            'post_content' => $reviewValues['_content'],
            'post_date' => $reviewValues['_date'],
            'post_date_gmt' => get_gmt_from_date($reviewValues['_date']),
            'post_name' => uniqid($reviewValues['_review_type']),
            'post_status' => $this->getNewPostStatus($reviewValues, $command->blacklisted),
            'post_title' => $reviewValues['_title'],
            'post_type' => Application::POST_TYPE,
        ];
        $postId = wp_insert_post($postValues, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($postValues);
            return false;
        }
        $post = get_post($postId);
        glsr()->action('review/creating', $post, $command);
        $this->setTerms($post->ID, $command->category);
        $review = $this->get($post);
        glsr()->action('review/created', $review, $command);
        return $review;
    }

    /**
     * @return Reviews
     */
    public function reviews(array $args = [])
    {
        $results = glsr(Query::class)->reviews($args);
        $reviews = $this->generateReviews($this->generatePosts($results));
        $total = $this->total($args, $results);
        glsr()->action('get/reviews', $reviews, $results);
        return new Reviews($reviews, $total, $args);
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
        glsr()->action('review/reverted', glsr_get_review($postId));
    }

    /**
     * @param WP_Post|int $reviewId
     * @return Review
     */
    public function get($reviewId)
    {
        $post = get_post($reviewId);
        if (glsr()->post_type !== Arr::get($post, 'post_type')) {
            $post = new WP_Post((object) []);
        }
        $review = new Review($post);
        glsr()->action('get/review', $review, $post);
        return $review;
    }

    /**
     * @return int
     */
    public function total(array $args = [], array $results = [])
    {
        return glsr(Query::class)->totalReviews($args, $results);
    }

    /**
     * @return array
     */
    protected function generatePosts(array $results)
    {
        $posts = array_map('get_post', $results);
        if (!wp_using_ext_object_cache()) {
            update_post_caches($posts, glsr()->post_type);
        }
        return $posts;
    }

    /**
     * @return array
     */
    protected function generateReviews(array $posts)
    {
        $reviews = array_map([$this, 'get'], $posts);
        $postIds = array_unique(call_user_func_array('array_merge', glsr_array_column($reviews, 'post_ids')));
        $termIds = array_unique(call_user_func_array('array_merge', glsr_array_column($reviews, 'term_ids')));
        update_postmeta_cache($postIds); // is this necessary to do for assigned post Ids?
        $lazyloader = wp_metadata_lazyloader();
        $lazyloader->queue_objects('term', $termIds); // term_ids for each review
        return $reviews;
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
        $termIds = Arr::convertFromString($commaSeparatedTermIds);
        foreach ($termIds as $termId) {
            if (is_numeric($termId)) {
                $termId = intval($termId);
            }
            $term = term_exists($termId, Application::TAXONOMY);
            if (!isset($term['term_id'])) {
                continue;
            }
            $terms[] = $term['term_id'];
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
