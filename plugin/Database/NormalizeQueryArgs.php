<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class NormalizeQueryArgs extends Arguments
{
    public $assigned_to;
    public $category;
    public $offset;
    public $order;
    public $orderby;
    public $page;
    public $per_page;
    public $post__in;
    public $post__not_in;
    public $rating;
    public $type;
    public $user;

    public function __construct(array $args = [])
    {
        $args = glsr(ReviewsDefaults::class)->merge($args);
        $args['assigned_to'] = Arr::uniqueInt(Arr::consolidate($args['assigned_to']));
        $args['category'] = $this->normalizeTermIds($args['category']);
        $args['offset'] = absint(filter_var($args['offset'], FILTER_SANITIZE_NUMBER_INT));
        $args['order'] = Str::restrictTo('ASC,DESC,', sanitize_key($args['order']), 'DESC'); // include an empty value
        $args['orderby'] = $this->normalizeOrderBy($args['orderby']);
        $args['page'] = absint($args['page']);
        $args['per_page'] = absint($args['per_page']); // "0" and "-1" = all
        $args['post__in'] = Arr::uniqueInt(Arr::consolidate($args['post__in']));
        $args['post__not_in'] = Arr::uniqueInt(Arr::consolidate($args['post__not_in']));
        $args['rating'] = absint(filter_var($args['rating'], FILTER_SANITIZE_NUMBER_INT));
        $args['type'] = sanitize_key($args['type']);
        $args['user'] = $this->normalizeUserIds(Arr::consolidate($args['user']));
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

    /**
     * @param array[]|string $termIds
     * @return array
     */
    public function normalizeTermIds($termIds)
    {
        return glsr(ReviewManager::class)->normalizeTermIds($termIds);
    }

    /**
     * @return array
     */
    protected function normalizeUserIds(array $users)
    {
        $userIds = [];
        foreach ($users as $userId) {
            if (!is_numeric($userId)) {
                $userId = Helper::castToInt(username_exists($userId));
            }
            $userIds[] = $userId;
        }
        return Arr::uniqueInt($userIds);
    }
}
