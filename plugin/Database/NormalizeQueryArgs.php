<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property int[] $assigned_posts;
 * @property int[] $assigned_terms;
 * @property int[] $assigned_users;
 * @property string $date_after;
 * @property string $date_before;
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
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['status'] = $this->normalizeStatus($args['status']);
        parent::__construct($args);
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
            'unapproved' => '0',
        ];
        $status = Str::restrictTo(array_keys($statuses), $value, 'approved', $strict = true);
        return $statuses[$status];
    }
}
