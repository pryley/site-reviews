<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * @property array $assigned_posts;
 * @property array $assigned_terms;
 * @property array $assigned_users;
 * @property int $offset;
 * @property string $order;
 * @property string $orderby;
 * @property int $page;
 * @property int $per_page;
 * @property array $post__in;
 * @property array $post__not_in;
 * @property int $rating;
 * @property string $type;
 */
class NormalizeQueryArgs extends Arguments
{
    public function __construct(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->restrict($args);
        $args['assigned_posts'] = glsr(PostManager::class)->normalizeIds($args['assigned_posts']);
        $args['assigned_terms'] = glsr(TaxonomyManager::class)->normalizeIds($args['assigned_terms']);
        $args['assigned_users'] = glsr(UserManager::class)->normalizeIds($args['assigned_users']);
        $args['offset'] = absint(filter_var($args['offset'], FILTER_SANITIZE_NUMBER_INT));
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['page'] = absint($args['page']);
        $args['per_page'] = absint($args['per_page']); // "0" and "-1" = all
        $args['post__in'] = Arr::uniqueInt($args['post__in']);
        $args['post__not_in'] = Arr::uniqueInt($args['post__not_in']);
        $args['rating'] = absint(filter_var($args['rating'], FILTER_SANITIZE_NUMBER_INT));
        $args['type'] = sanitize_key($args['type']);
        parent::__construct($args);
    }

    /**
     * @return string
     */
    protected function normalizeOrderBy($orderBy)
    {
        $orderBy = Str::restrictTo('author,comment_count,date,ID,menu_order,none,rand,relevance', $orderBy, 'date');
        if (in_array($orderBy, ['comment_count', 'ID', 'menu_order'])) {
            return Str::prefix('p.', $orderBy);
        }
        if (in_array($orderBy, ['author', 'date'])) {
            return Str::prefix('p.post_', $orderBy);
        }
        return $orderBy;
    }
}
