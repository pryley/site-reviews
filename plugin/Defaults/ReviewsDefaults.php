<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ReviewsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'terms' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'order' => ['asc', 'desc'],
        'orderby' => [
            'author',
            'comment_count',
            'date',
            'date_gmt',
            'id',
            'menu_order',
            'none',
            'random',
            'rating',
        ],
        'status' => ['all', 'approved', 'pending', 'publish', 'unapproved'],
        'terms' => ['0', 'false', '1', 'true'],
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'assigned_to' => 'assigned_posts',
        'author_id' => 'user__in',
        'category' => 'assigned_terms',
        'count' => 'per_page', // @deprecated in v4.1.0
        'display' => 'per_page',
        'exclude' => 'post__not_in',
        'include' => 'post__in',
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_posts_types' => 'array-string',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'content' => 'text-multiline',
        'email' => 'email',
        'integration' => 'slug',
        'ip_address' => 'ip-address',
        'offset' => 'min:0',
        'order' => 'name',
        'orderby' => 'name',
        'page' => 'min:1',
        'per_page' => 'min:-1', // -1 means unlimited
        'post__in' => 'array-int',
        'post__not_in' => 'array-int',
        'rating' => 'rating',
        'rating_field' => 'name',
        'status' => 'name',
        'type' => 'slug',
        'user__in' => 'user-ids',
        'user__not_in' => 'user-ids',
    ];

    protected function defaults(): array
    {
        return [
            'assigned_posts' => '',
            'assigned_posts_types' => [],
            'assigned_terms' => '',
            'assigned_users' => '',
            'content' => '',
            'date' => '', // can be an array or string
            'email' => '',
            'integration' => '', // the slug of the integration querying the reviews
            'ip_address' => '',
            'offset' => 0,
            'order' => 'desc',
            'orderby' => 'date',
            'page' => 1,
            'per_page' => 10,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'rating_field' => 'rating', // used for custom rating fields
            'status' => 'approved',
            'terms' => '',
            'type' => '',
            'user__in' => [],
            'user__not_in' => [],
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        if ($postIds = Arr::getAs('array', $values, 'assigned_posts')) {
            $values['assigned_posts_types'] = [];
            foreach ($postIds as $postType) {
                if (!is_numeric($postType) && post_type_exists($postType)) {
                    $values['assigned_posts'] = []; // query only by assigned post types!
                    $values['assigned_posts_types'][] = $postType;
                }
            }
        } else {
            $postTypes = glsr(Sanitizer::class)->sanitizeArrayString(Arr::get($values, 'assigned_posts_types'));
            $values['assigned_posts_types'] = array_filter($postTypes, 'post_type_exists');
        }
        return $values;
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $values['assigned_posts'] = glsr(Multilingual::class)->getPostIdsForAllLanguages($values['assigned_posts']);
        $values['assigned_terms'] = glsr(Multilingual::class)->getTermIdsForAllLanguages($values['assigned_terms']);
        $values['date'] = $this->finalizeDate($values['date']);
        $values['order'] = $this->finalizeOrder($values['order']);
        $values['orderby'] = $this->finalizeOrderby($values['orderby']);
        $values['status'] = $this->finalizeStatus($values['status']);
        $values['terms'] = $this->finalizeTerms($values['terms']);
        return $values;
    }

    protected function finalizeDate($value): array
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

    protected function finalizeOrder(string $value): string
    {
        return strtoupper($value);
    }

    protected function finalizeOrderby(string $value): string
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

    protected function finalizeStatus(string $value): int
    {
        $statuses = [
            'all' => -1,
            'approved' => 1,
            'pending' => 0,
            'publish' => 1,
            'unapproved' => 0,
        ];
        return $statuses[$value];
    }

    protected function finalizeTerms(string $value): int
    {
        if (!empty($value)) {
            return Cast::toInt(Cast::toBool($value));
        }
        return -1;
    }
}
