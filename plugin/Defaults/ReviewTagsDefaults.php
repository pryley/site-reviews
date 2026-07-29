<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * A tag is a key of the review context — what {{ tag }} interpolates in
 * a review template.
 *
 * This list is not the census of what exists: ReviewTags::names() unions
 * it with the context an actual build produces, so a tag added through
 * review/build/after alone still counts as a tag.
 */
class ReviewTagsDefaults extends DefaultsAbstract
{
    public const DESCRIPTOR = [
        // Offer it in a layout builder's palette (Review Themes).
        'display' => false,
        // Offer it in a review template editor (Review Forms).
        'insert' => true,
        // Either "review" or "form"
        'source' => 'review',
    ];

    protected function defaults(): array
    {
        return [
            // The JSON blob of assignments, read by scripts off the wrapper element
            'assigned' => ['insert' => false],
            'assigned_links' => ['display' => true],
            'assigned_posts' => ['display' => true],
            'assigned_terms' => ['display' => true],
            'assigned_users' => ['display' => true],
            'author' => ['display' => true],
            'author_id' => [],
            'avatar' => ['display' => true],
            'content' => ['display' => true, 'source' => 'form'],
            // The aggregate of every custom field value
            'custom' => ['insert' => false],
            'date' => ['display' => true],
            'date_gmt' => [],
            'email' => [],
            'ip_address' => [],
            'is_approved' => [],
            'is_modified' => [],
            'is_pinned' => [],
            'location' => ['display' => true],
            // The author name under its pre-mapping key (ReviewDefaults maps name => author)
            'name' => [],
            'rating' => ['display' => true],
            'rating_id' => [],
            'response' => ['display' => true],
            'review_id' => [],
            'score' => [],
            'status' => [],
            'terms' => [],
            'title' => ['display' => true, 'source' => 'form'],
            'type' => ['display' => true],
            'url' => [],
            'verified' => ['display' => true],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        foreach ($values as $tag => $descriptor) {
            $values[$tag] = wp_parse_args(Arr::consolidate($descriptor), static::DESCRIPTOR);
        }
        ksort($values);
        return $values;
    }
}
