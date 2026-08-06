<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ExperimentsController implements ControllerContract
{
    use HookProxy;

    public array $savedQueries = [];

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
     * Answer faithfully, or leave the query to WordPress: this filter answers
     * a query only when every query var has a faithful review-query
     * translation. All other queries fall through to the comments table.
     *
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
        if (!$this->isProductQuery($vars)) {
            return $data;
        }
        if (!$this->canTranslateQuery($vars)) {
            glsr_log()->debug('The wp_comments experiment left a comment query to WordPress because its query vars have no faithful translation.', $query->query_vars);
            return $data;
        }
        $args = $this->getReviewArgs($vars);
        $count = true === $vars->count;
        $ids = 'ids' === $vars->fields;
        $hash = md5(maybe_serialize(compact('args', 'count', 'ids')));
        if (!array_key_exists($hash, $this->savedQueries)) {
            $reviews = glsr_get_reviews($args);
            if ($count) {
                $result = $this->getReviewsCount($reviews);
            } elseif ($ids) {
                // when "fields" is "ids", the comments_pre_query contract is an array of ints
                $result = wp_list_pluck($this->getReviews($reviews), 'comment_ID');
            } else {
                $result = $this->getReviews($reviews);
            }
            $this->savedQueries[$hash] = $result;
        }
        return $this->savedQueries[$hash];
    }

    /**
     * Only the comments table can answer identity-bound vars (comment IDs,
     * hierarchy, comment meta such as the _review_order_id ledger). The other
     * vars have no review-query equivalent yet.
     */
    protected function canTranslateQuery(Arguments $args): bool
    {
        if (!in_array($args->fields, ['', 'ids', null], true)) {
            return false;
        }
        if (!empty($args->author__in) && !empty($args->user_id)) {
            return false; // two ANDed author constraints cannot be expressed
        }
        if (!empty($args->post_id) && !empty($args->post__in)) {
            return false; // two ANDed post constraints cannot be expressed
        }
        if (is_null($this->translatedOrderby($args))) {
            return false;
        }
        if (is_null($this->translatedStatus($args))) {
            return false;
        }
        if (!$this->isTranslatableType($args)) {
            return false;
        }
        $untranslatable = [
            'author_url',
            'comment__in',
            'comment__not_in',
            'date_query',
            'include_unapproved',
            'karma',
            'meta_key',
            'meta_query',
            'meta_value',
            'parent',
            'parent__in',
            'parent__not_in',
            'post__not_in',
            'post_author',
            'post_author__in',
            'post_author__not_in',
            'post_name',
            'post_parent',
            'post_status',
            'search',
        ];
        foreach ($untranslatable as $key) {
            if (!empty($args->$key)) {
                return false;
            }
        }
        return true;
    }

    protected function getReviewArgs(Arguments $args): array
    {
        $number = Cast::toInt($args->number);
        $params = [
            'offset' => $args->offset,
            'order' => strtolower(Cast::toString($args->get('order', 'desc'))),
            'orderby' => $this->translatedOrderby($args),
            'page' => $args->get('paged', 1),
            'per_page' => $number > 0 ? $number : -1, // an empty "number" means all
            'status' => $this->translatedStatus($args),
        ];
        if (!empty($args->author_email)) {
            $params['email'] = $args->author_email;
        }
        if ($userIn = Arr::uniqueInt($args->author__in)) {
            $params['user__in'] = $userIn;
        } elseif (!empty($args->user_id)) {
            $params['user__in'] = Arr::uniqueInt([$args->user_id]);
        }
        if ($userNotIn = Arr::uniqueInt($args->author__not_in)) {
            $params['user__not_in'] = $userNotIn;
        }
        if (!empty($args->post_id)) {
            $params['assigned_posts'] = $args->post_id;
        } elseif ($postIn = Arr::uniqueInt($args->post__in)) {
            $params['assigned_posts'] = $postIn;
        } else {
            $params['assigned_posts'] = 'product';
        }
        return $params;
    }

    protected function getReviews(Reviews $reviews): array
    {
        $data = [];
        foreach ($reviews as $review) {
            $comment = new \WP_Comment((object) [ // @phpstan-ignore-line
                'comment_agent' => '',
                'comment_approved' => (string) (int) $review->is_approved, // '0'|'1', the WP convention
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
            $comment->populated_children(true); // prevents infinite recursion
            $data[] = $comment;
        }
        return $data;
    }

    protected function getReviewsCount(Reviews $reviews): int
    {
        return (int) $reviews->total;
    }

    protected function isProductQuery(Arguments $args): bool
    {
        return 'review' === $args->type
            || 'product' === $args->post_type
            || 'product' === get_post_type($args->post_id);
    }

    protected function isTranslatableType(Arguments $args): bool
    {
        $types = Arr::consolidate($args->type ?: []);
        if (!empty(array_diff($types, ['review']))) {
            return false;
        }
        $typeIn = Arr::consolidate($args->type__in ?: []);
        if (!empty(array_diff($typeIn, ['review']))) {
            return false;
        }
        return empty($args->type__not_in);
    }

    /**
     * An empty orderby means comment_date_gmt (the WP_Comment_Query default).
     */
    protected function translatedOrderby(Arguments $args): ?string
    {
        $orderbys = [
            '' => 'date_gmt',
            'comment_ID' => 'id',
            'comment_date' => 'date',
            'comment_date_gmt' => 'date_gmt',
            'none' => 'none',
        ];
        $orderby = $args->orderby;
        if (false === $orderby || [] === $orderby) {
            return 'none'; // both disable the ORDER BY clause
        }
        if (!is_scalar($orderby ?? '')) {
            return null;
        }
        return Arr::get($orderbys, Cast::toString($orderby), null);
    }

    /**
     * An empty status means all (the WP_Comment_Query default).
     */
    protected function translatedStatus(Arguments $args): ?string
    {
        $statuses = [
            '' => 'all',
            '0' => 'unapproved',
            '1' => 'approved',
            'all' => 'all',
            'approve' => 'approved',
            'hold' => 'unapproved',
        ];
        $status = $args->status;
        if (!is_scalar($status ?? '')) {
            return null; // status arrays and comma-separated lists are not translated
        }
        return Arr::get($statuses, Cast::toString($status), null);
    }
}
