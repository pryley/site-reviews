<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property int[] $assigned_posts;
 * @property int[] $assigned_terms;
 * @property int[] $assigned_users;
 * @property string|array $date;
 * @property string $email;
 * @property string $ip_address;
 * @property int $offset;
 * @property string $order;
 * @property string $orderby;
 * @property int $page;
 * @property string $pagination;
 * @property int $per_page;
 * @property int[] $post__in;
 * @property int[] $post__not_in;
 * @property int $rating;
 * @property string $status;
 * @property string $type;
 * @property int[] $user__in;
 * @property int[] $user__not_in;
 */
class NormalizeQueryArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->restrict($args);
        $args['assigned_posts'] = glsr(PostManager::class)->normalizeIds($args['assigned_posts']);
        $args['assigned_terms'] = glsr(TaxonomyManager::class)->normalizeIds($args['assigned_terms']);
        $args['assigned_users'] = glsr(UserManager::class)->normalizeIds($args['assigned_users']);
        $args['date'] = $this->normalizeDate($args['date']);
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['status'] = $this->normalizeStatus($args['status']);
        parent::__construct($args);
    }

    /**
     * @param string|array $value
     * @return array
     */
    protected function normalizeDate($value)
    {
        $date = array_fill_keys(['after', 'before', 'day', 'inclusive', 'month', 'year'], '');
        $timestamp = strtotime(Cast::toString($value));
        if (false !== $timestamp) {
            $date['year'] = date('Y', $timestamp);
            $date['month'] = date('n', $timestamp);
            $date['day'] = date('j', $timestamp);
            return $date;
        }
        if (false !== strtotime(Arr::get($value, 'after'))) {
            $date['after'] = $value['after'];
        }
        if (false !== strtotime(Arr::get($value, 'before'))) {
            $date['before'] = $value['before'];
        }
        if (!empty(array_filter($date))) {
            $date['inclusive'] = Cast::toBool(Arr::get($value, 'inclusive')) ? '=' : '';
        }
        return $date;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function normalizeOrderBy($value)
    {
        $orderBy = Str::restrictTo('author,comment_count,date,date_gmt,ID,menu_order,none,random,rating,relevance', $value, 'date');
        if (in_array($orderBy, ['comment_count', 'ID', 'menu_order'])) {
            return Str::prefix($orderBy, 'p.');
        }
        if (in_array($orderBy, ['author', 'date', 'date_gmt'])) {
            return Str::prefix($orderBy, 'p.post_');
        }
        if (in_array($orderBy, ['rating'])) {
            return Str::prefix($orderBy, 'r.');
        }
        return $orderBy;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function normalizeStatus($value)
    {
        $statuses = [
            'all' => '',
            'approved' => '1',
            'pending' => '0',
            'publish' => '1',
            'unapproved' => '0',
        ];
        $status = Str::restrictTo(array_keys($statuses), $value, 'approved', $strict = true);
        return $statuses[$status];
    }
}
