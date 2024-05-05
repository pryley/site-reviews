<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ExperimentsController implements ControllerContract
{
    use HookProxy;

    /**
     * @param mixed  $value
     * @param int    $objectId
     * @param string $metaKey
     * @param bool   $single
     *
     * @return mixed
     *
     * @filter get_comment_metadata
     */
    public function filterProductCommentMeta($value, $objectId, $metaKey, $single)
    {
        if (Review::isReview($objectId)) {
            $review = glsr_get_review($objectId);
            $value = $review[$metaKey];
            return $single ? $value : [$value];
        }
        return $value;
    }

    /**
     * @param mixed             $data
     * @param \WP_Comment_Query $query
     *
     * @return mixed
     *
     * @filter comments_pre_query
     */
    public function filterProductCommentsQuery($data, $query)
    {
        $vars = glsr()->args($query->query_vars);
        $isProductQuery = 'review' === $vars->type || ('product' === $vars->post_type || 'product' === get_post_type($vars->post_id));
        if (!$isProductQuery) {
            return $data;
        }
        $args = $this->getReviewArgs($vars);
        $reviews = glsr_get_reviews($args);
        if (true === $vars->count) {
            return $this->getReviewsCount($reviews);
        }
        return $this->getReviews($reviews);
    }

    protected function getReviewArgs(Arguments $args): array
    {
        $statuses = [
            'all' => 'all',
            'approve' => 'approved',
            'hold' => 'unapproved',
        ];
        $params = [
            'offset' => $args->offset,
            'page' => $args->paged,
            'per_page' => $args->get('number', 10),
            'status' => Arr::get($statuses, $args->status, 'approved'),
        ];
        if (!empty($args->post_id)) {
            $params['assigned_posts'] = $args->post_id;
        } else {
            $params['assigned_posts'] = 'product';
        }
        return $params;
    }

    protected function getReviews(Reviews $reviews): array
    {
        $data = [];
        foreach ($reviews as $review) {
            $data[] = new \WP_Comment((object) [ // @phpstan-ignore-line
                'comment_agent' => '',
                'comment_approved' => (string) $review->is_approved,
                'comment_author' => $review->name,
                'comment_author_email' => $review->email,
                'comment_author_IP' => $review->ip_address,
                'comment_author_url' => '',
                'comment_content' => $review->content,
                'comment_date' => $review->date,
                'comment_date_gmt' => $review->date_gmt,
                'comment_ID' => $review->ID,
                'comment_karma' => 0,
                'comment_parent' => 0,
                'comment_post_ID' => Arr::get($review->assigned_posts, 0),
                'comment_type' => 'review',
                'user_id' => $review->user_id,
            ]);
        }
        return $data;
    }

    protected function getReviewsCount(Reviews $reviews): int
    {
        return (int) $reviews->total;
    }
}
