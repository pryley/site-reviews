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
     * @return string
     */
    public function buildColumnAssignedTo($postId)
    {
        $assignedPost = glsr(Database::class)->getAssignedToPost($postId);
        $column = '&mdash;';
        if ($assignedPost instanceof WP_Post && 'publish' == $assignedPost->post_status) {
            $column = glsr(Builder::class)->a(get_the_title($assignedPost->ID), [
                'href' => (string) get_the_permalink($assignedPost->ID),
            ]);
        }
        return $column;
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
        return glsr(Builder::class)->i([
            'class' => $pinned.'dashicons dashicons-sticky',
            'data-id' => $postId,
        ]);
    }

    /**
     * @param int $postId
     * @return string
     */
    public function buildColumnReviewer($postId)
    {
        return strval(glsr(Database::class)->get($postId, 'author'));
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
        $method = glsr(Helper::class)->buildMethodName($column, 'buildColumn');
        echo method_exists($this, $method)
            ? call_user_func([$this, $method], $postId)
            : apply_filters('site-reviews/columns/'.$column, '', $postId);
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
