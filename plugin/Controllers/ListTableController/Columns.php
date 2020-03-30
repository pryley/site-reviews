<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableController;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use WP_Post;

class Columns
{
    /**
     * @param int $postId
     * @return void|string
     */
    public function buildColumnAssignedTo($postId)
    {
        $assignedPost = glsr(Database::class)->getAssignedToPost($postId);
        if ($assignedPost instanceof WP_Post && 'publish' == $assignedPost->post_status) {
            return glsr(Builder::class)->a(get_the_title($assignedPost->ID), [
                'href' => (string) get_the_permalink($assignedPost->ID),
            ]);
        }
    }

    /**
     * @param int $postId
     * @return void|string
     */
    public function buildColumnEmail($postId)
    {
        if ($email = glsr(Database::class)->get($postId, 'email')) {
            return $email;
        }
    }

    /**
     * @param int $postId
     * @return void|string
     */
    public function buildColumnIpAddress($postId)
    {
        if ($ipAddress = glsr(Database::class)->get($postId, 'ip_address')) {
            return $ipAddress;
        }
    }

    /**
     * @param int $postId
     * @return string
     */
    public function buildColumnPinned($postId)
    {
        $pinned = glsr(Database::class)->get($postId, 'pinned')
            ? 'pinned '
            : '';
        if (glsr()->can('edit_others_posts')) {
            $pinned.= 'pin-review ';
        }
        return glsr(Builder::class)->i([
            'class' => $pinned.'dashicons dashicons-sticky',
            'data-id' => $postId,
        ]);
    }

    /**
     * @param int $postId
     * @return string
     */
    public function buildColumnResponse($postId)
    {
        return glsr(Database::class)->get($postId, 'response')
            ? __('Yes', 'site-reviews')
            : __('No', 'site-reviews');
    }

    /**
     * @param int $postId
     * @return string
     */
    public function buildColumnReviewer($postId)
    {
        $author = strval(glsr(Database::class)->get($postId, 'author'));
        $userId = Helper::castToInt(get_post($postId)->post_author);
        return !empty($userId)
            ? glsr(Builder::class)->a($author, ['href' => get_author_posts_url($userId)])
            : $author;
    }

    /**
     * @param int $postId
     * @param int|null $rating
     * @return string
     */
    public function buildColumnRating($postId)
    {
        return glsr_star_rating(intval(glsr(Database::class)->get($postId, 'rating')));
    }

    /**
     * @param int $postId
     * @return string
     */
    public function buildColumnReviewType($postId)
    {
        $type = glsr(Database::class)->get($postId, 'review_type');
        return array_key_exists($type, glsr()->reviewTypes)
            ? glsr()->reviewTypes[$type]
            : __('Unsupported Type', 'site-reviews');
    }

    /**
     * @param string $postType
     * @return void
     */
    public function renderFilters($postType)
    {
        if (Application::POST_TYPE !== $postType) {
            return;
        }
        if (!($status = filter_input(INPUT_GET, 'post_status'))) {
            $status = 'publish';
        }
        $ratings = glsr(Database::class)->getReviewsMeta('rating', $status);
        $types = glsr(Database::class)->getReviewsMeta('review_type', $status);
        $this->renderFilterRatings($ratings);
        $this->renderFilterTypes($types);
    }

    /**
     * @param string $column
     * @param int $postId
     * @return void
     */
    public function renderValues($column, $postId)
    {
        $method = Helper::buildMethodName($column, 'buildColumn');
        $value = method_exists($this, $method)
            ? call_user_func([$this, $method], $postId)
            : '';
        $value = apply_filters('site-reviews/columns/'.$column, $value, $postId);
        if (0 !== $value && empty($value)) {
            $value = '&mdash;';
        }
        echo $value;
    }

    /**
     * @param array $ratings
     * @return void
     */
    protected function renderFilterRatings($ratings)
    {
        if (empty($ratings)) {
            return;
        }
        $ratings = array_flip(array_reverse($ratings));
        array_walk($ratings, function (&$value, $key) {
            $label = _n('%s star', '%s stars', $key, 'site-reviews');
            $value = sprintf($label, $key);
        });
        echo glsr(Builder::class)->label(__('Filter by rating', 'site-reviews'), [
            'class' => 'screen-reader-text',
            'for' => 'rating',
        ]);
        echo glsr(Builder::class)->select([
            'name' => 'rating',
            'options' => ['' => __('All ratings', 'site-reviews')] + $ratings,
            'value' => filter_input(INPUT_GET, 'rating'),
        ]);
    }

    /**
     * @param array $types
     * @return void
     */
    protected function renderFilterTypes($types)
    {
        if (count(glsr()->reviewTypes) < 2) {
            return;
        }
        echo glsr(Builder::class)->label(__('Filter by type', 'site-reviews'), [
            'class' => 'screen-reader-text',
            'for' => 'review_type',
        ]);
        echo glsr(Builder::class)->select([
            'name' => 'review_type',
            'options' => ['' => __('All types', 'site-reviews')] + glsr()->reviewTypes,
            'value' => filter_input(INPUT_GET, 'review_type'),
        ]);
    }
}
