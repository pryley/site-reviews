<?php

namespace GeminiLabs\SiteReviews\Database;

class CountManager
{
    const META_AVERAGE = '_glsr_average';
    const META_RANKING = '_glsr_ranking';
    const META_REVIEWS = '_glsr_reviews';

    /**
     * @param int $postId
     * @return void
     */
    public function posts($postId)
    {
        $counts = glsr_get_ratings(['assigned_posts' => $postId]);
        update_post_meta($postId, static::META_AVERAGE, $counts->average);
        update_post_meta($postId, static::META_RANKING, $counts->ranking);
        update_post_meta($postId, static::META_REVIEWS, $counts->reviews);
    }

    /**
     * @param int $termId
     * @return void
     */
    public function terms($termId)
    {
        $counts = glsr_get_ratings(['assigned_terms' => $termId]);
        update_term_meta($termId, static::META_AVERAGE, $counts->average);
        update_term_meta($termId, static::META_RANKING, $counts->ranking);
        update_term_meta($termId, static::META_REVIEWS, $counts->reviews);
    }

    /**
     * @param int $userId
     * @return void
     */
    public function users($userId)
    {
        $counts = glsr_get_ratings(['assigned_users' => $userId]);
        update_user_meta($userId, static::META_AVERAGE, $counts->average);
        update_user_meta($userId, static::META_RANKING, $counts->ranking);
        update_user_meta($userId, static::META_REVIEWS, $counts->reviews);
    }
}
