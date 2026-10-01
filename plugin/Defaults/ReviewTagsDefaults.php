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
                'description' => _x('The assignments as JSON data (same as assigned_data).', 'admin-text', 'site-reviews'),
                'insert' => false,
            ],
            'assigned_data' => [
                'description' => _x('The posts, categories and users the review is assigned to, as JSON data.', 'admin-text', 'site-reviews'),
                'insert' => true,
            ],
            'assigned_links' => [
                'description' => _x('Links to the posts the review is assigned to.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Assigned Links', 'admin-text', 'site-reviews'),
            ],
            'assigned_posts' => [
                'description' => _x('The titles of the posts the review is assigned to.', 'admin-text', 'site-reviews'),
                'display' => true,
                'insert' => true,
                'label' => _x('Assigned Posts', 'admin-text', 'site-reviews'),
            ],
            'assigned_terms' => [
                'description' => _x('The names of the categories the review is assigned to.', 'admin-text', 'site-reviews'),
                'display' => true,
                'insert' => true,
                'label' => _x('Assigned Terms', 'admin-text', 'site-reviews'),
            ],
            'assigned_users' => [
                'description' => _x('The names of the users the review is assigned to.', 'admin-text', 'site-reviews'),
                'display' => true,
                'insert' => true,
                'label' => _x('Assigned Users', 'admin-text', 'site-reviews'),
            ],
            'author' => [
                'description' => _x('The name of the reviewer.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Author Name', 'admin-text', 'site-reviews'),
            ],
            'author_id' => [
                'description' => _x('The user ID of the reviewer, or 0 if they were not logged in.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'avatar' => [
                'description' => _x('The avatar image of the reviewer.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Avatar', 'admin-text', 'site-reviews'),
            ],
            'content' => [
                'description' => _x('The body of the review.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Content', 'admin-text', 'site-reviews'),
            ],
            'custom' => [ // The aggregate of every custom field value
                'description' => _x('The values of every custom field, as data.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'date' => [
                'description' => _x('The date the review was submitted.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Date', 'admin-text', 'site-reviews'),
            ],
            'date_gmt' => [
                'description' => _x('The date the review was submitted, in UTC.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'email' => [
                'description' => _x('The email address of the reviewer.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'ip_address' => [
                'description' => _x('The IP address the review was submitted from.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'is_approved' => [
                'description' => _x('Whether the review has been approved.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'is_modified' => [
                'description' => _x('Whether the review has been edited since it was submitted.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'is_pinned' => [
                'description' => _x('Whether the review is pinned.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'location' => [
                'description' => _x('Where the review was submitted from.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Location', 'admin-text', 'site-reviews'),
            ],
            'name' => [
                'description' => _x('The name of the reviewer (same as author).', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'rating' => [
                'description' => _x('The star rating of the review.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Rating', 'admin-text', 'site-reviews'),
            ],
            'rating_id' => [
                'description' => _x('The internal ID of the rating record.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'response' => [
                'description' => _x('Your response to the review.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Response', 'admin-text', 'site-reviews'),
            ],
            'review_id' => [
                'description' => _x('The ID of the review.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'score' => [
                'description' => _x('How many people found the review helpful.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'status' => [
                'description' => _x('The status of the review.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => false,
            ],
            'terms' => [
                'description' => _x('Whether the reviewer accepted the terms.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'title' => [
                'description' => _x('The title of the review.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Title', 'admin-text', 'site-reviews'),
            ],
            'type' => [
                'description' => _x('The review type (local or from an imported source).', 'admin-text', 'site-reviews'),
                'display' => true,
                'insert' => true,
                'label' => _x('Review Type', 'admin-text', 'site-reviews'),
            ],
            'url' => [
                'description' => _x('The link the review was imported from.', 'admin-text', 'site-reviews'),
                'display' => false,
                'insert' => true,
            ],
            'verified' => [
                'description' => _x('Whether the reviewer has been verified.', 'admin-text', 'site-reviews'),
                'display' => true,
                'group' => 'default',
                'insert' => true,
                'label' => _x('Verified', 'admin-text', 'site-reviews'),
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
