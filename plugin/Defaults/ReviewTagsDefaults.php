<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * A tag is a key of the review context — what {{ tag }} interpolates in
 * a review template.
 *
 * This list is not the census of what exists: ReviewTags::names() unions
 * it with the context an actual build produces, so a tag added through
 * "site-reviews/review/build/after" alone still counts as a tag.
 */
class ReviewTagsDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'assigned' => [ // @compat v8.1 - renamed to assigned_data
                'insert' => false,
                'label' => _x('The assignments as JSON data, under the name it had before assigned_data.', 'admin-text', 'site-reviews'),
            ],
            'assigned_data' => [
                'insert' => true,
                'label' => _x('The posts, categories and users the review is assigned to, as JSON data.', 'admin-text', 'site-reviews'),
            ],
            'assigned_links' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Links to the posts the review is assigned to.', 'admin-text', 'site-reviews'),
            ],
            'assigned_posts' => [
                'display' => true,
                'insert' => true,
                'label' => _x('The titles of the posts the review is assigned to.', 'admin-text', 'site-reviews'),
            ],
            'assigned_terms' => [
                'display' => true,
                'insert' => true,
                'label' => _x('The names of the categories the review is assigned to.', 'admin-text', 'site-reviews'),
            ],
            'assigned_users' => [
                'display' => true,
                'insert' => true,
                'label' => _x('The names of the users the review is assigned to.', 'admin-text', 'site-reviews'),
            ],
            'author' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The name of the reviewer.', 'admin-text', 'site-reviews'),
            ],
            'author_id' => [
                'display' => false,
                'insert' => true,
                'label' => _x('The user ID of the reviewer, or 0 if they were not logged in.', 'admin-text', 'site-reviews'),
            ],
            'avatar' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The avatar image of the reviewer.', 'admin-text', 'site-reviews'),
            ],
            'content' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The body of the review.', 'admin-text', 'site-reviews'),
            ],
            'custom' => [ // The aggregate of every custom field value
                'display' => false,
                'insert' => false,
                'label' => _x('The values of every custom field, as data.', 'admin-text', 'site-reviews'),
            ],
            'date' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The date the review was submitted.', 'admin-text', 'site-reviews'),
            ],
            'date_gmt' => [
                'display' => false,
                'insert' => false,
                'label' => _x('The date the review was submitted, in UTC.', 'admin-text', 'site-reviews'),
            ],
            'email' => [
                'display' => false,
                'insert' => false,
                'label' => _x('The email address of the reviewer.', 'admin-text', 'site-reviews'),
            ],
            'ip_address' => [
                'display' => false,
                'insert' => false,
                'label' => _x('The IP address the review was submitted from.', 'admin-text', 'site-reviews'),
            ],
            'is_approved' => [
                'display' => false,
                'insert' => true,
                'label' => _x('Whether the review has been approved.', 'admin-text', 'site-reviews'),
            ],
            'is_modified' => [
                'display' => false,
                'insert' => true,
                'label' => _x('Whether the review has been edited since it was submitted.', 'admin-text', 'site-reviews'),
            ],
            'is_pinned' => [
                'display' => false,
                'insert' => true,
                'label' => _x('Whether the review is pinned.', 'admin-text', 'site-reviews'),
            ],
            'location' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Where the review was submitted from.', 'admin-text', 'site-reviews'),
            ],
            'name' => [
                'display' => false,
                'insert' => false,
                'label' => _x('The name of the reviewer, under the key it is stored with.', 'admin-text', 'site-reviews'),
            ],
            'rating' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The star rating of the review.', 'admin-text', 'site-reviews'),
            ],
            'rating_id' => [
                'display' => false,
                'insert' => true,
                'label' => _x('The internal ID of the rating record.', 'admin-text', 'site-reviews'),
            ],
            'response' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Your response to the review.', 'admin-text', 'site-reviews'),
            ],
            'review_id' => [
                'display' => false,
                'insert' => true,
                'label' => _x('The ID of the review.', 'admin-text', 'site-reviews'),
            ],
            'score' => [
                'display' => false,
                'insert' => true,
                'label' => _x('How many people found the review helpful.', 'admin-text', 'site-reviews'),
            ],
            'status' => [
                'display' => false,
                'insert' => false,
                'label' => _x('The publication status of the review.', 'admin-text', 'site-reviews'),
            ],
            'terms' => [
                'display' => false,
                'insert' => true,
                'label' => _x('Whether the reviewer accepted the terms.', 'admin-text', 'site-reviews'),
            ],
            'title' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('The title of the review.', 'admin-text', 'site-reviews'),
            ],
            'type' => [
                'display' => true,
                'insert' => true,
                'label' => _x('Where the review came from: your site, or an imported source.', 'admin-text', 'site-reviews'),
            ],
            'url' => [
                'display' => false,
                'insert' => true,
                'label' => _x('The link the review was imported from.', 'admin-text', 'site-reviews'),
            ],
            'verified' => [
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Whether the reviewer has been verified.', 'admin-text', 'site-reviews'),
            ],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        foreach ($values as $tag => $descriptor) {
            $values[$tag] = glsr(ReviewTagDefaults::class)->call('restrict', Arr::consolidate($descriptor));
        }
        ksort($values);
        return $values;
    }
}
