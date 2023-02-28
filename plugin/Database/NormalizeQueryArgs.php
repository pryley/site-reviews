<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * @property int[] $assigned_posts;
 * @property array $assigned_post_types;
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
 * @property string $rating_field;
 * @property string $status;
 * @property string $terms;
 * @property string $type;
 * @property int[] $user__in;
 * @property int[] $user__not_in;
 */
class NormalizeQueryArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->restrict($args);
        $args['assigned_posts'] = glsr(Multilingual::class)->getPostIds($args['assigned_posts']);
        $args['date'] = $this->normalizeDate($args['date']);
        $args['order'] = strtoupper($args['order']);
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['status'] = $this->normalizeStatus($args['status']);
        parent::__construct($args);
    }

    /**
     * @param string|array $value
     */
    protected function normalizeDate($value): array
    {
        $date = array_fill_keys(['after', 'before', 'day', 'inclusive', 'month', 'year'], '');
        $timestamp = strtotime(Cast::toString($value));
        if (false !== $timestamp) {
            $date['year'] = date('Y', $timestamp);
            $date['month'] = date('n', $timestamp);
            $date['day'] = date('j', $timestamp);
            return $date;
        }
        $date['after'] = glsr(Sanitizer::class)->sanitizeDate(Arr::get($value, 'after'));
        $date['before'] = glsr(Sanitizer::class)->sanitizeDate(Arr::get($value, 'before'));
        if (!empty(array_filter($date))) {
            $date['inclusive'] = Arr::getAs('bool', $value, 'inclusive') ? '=' : '';
        }
        return $date;
    }

    protected function normalizeOrderBy(string $value): string
    {
        if ('id' === $value) {
            return 'p.ID';
        }
        if (in_array($value, ['comment_count', 'menu_order'])) {
            return Str::prefix($value, 'p.');
        }
        if (in_array($value, ['author', 'date', 'date_gmt'])) {
            return Str::prefix($value, 'p.post_');
        }
        if (in_array($value, ['rating'])) {
            return Str::prefix($value, 'r.');
        }
        return $value;
    }

    protected function normalizeStatus(string $value): string
    {
        $statuses = [
            'all' => '-1',
            'approved' => '1',
            'pending' => '0',
            'publish' => '1',
            'unapproved' => '0',
        ];
        return $statuses[$value];
    }
}
