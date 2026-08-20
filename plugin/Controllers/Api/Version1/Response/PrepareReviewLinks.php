<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Response;

use GeminiLabs\SiteReviews\Review;

class PrepareReviewLinks
{
    public string $namespace;

    public string $restBase;

    public function __construct(string $namespace, string $restBase)
    {
        $this->namespace = $namespace;
        $this->restBase = $restBase;
    }

    public function editLinks(Review $review): array
    {
        $links = [];
        $reviewRestUrl = rest_url(trailingslashit("{$this->namespace}/{$this->restBase}").$review->ID);
        $taxonomy = get_taxonomy(glsr()->taxonomy);
        if (glsr()->can('publish_posts')) {
            $links['https://api.w.org/action-publish'] = [
                'href' => $reviewRestUrl,
            ];
        }
        if (glsr()->can('edit_others_posts')) {
            $links['https://api.w.org/action-assign-author'] = [
                'href' => $reviewRestUrl,
            ];
        }
        if (current_user_can($taxonomy->cap->edit_terms)) {
            $links['https://api.w.org/action-create-'.glsr()->taxonomy] = [
                'href' => $reviewRestUrl,
            ];
        }
        if (current_user_can($taxonomy->cap->assign_terms)) {
            $links['https://api.w.org/action-assign-'.glsr()->taxonomy] = [
                'href' => $reviewRestUrl,
            ];
        }
        return $links;
    }

    public function links(Review $review): array
    {
        $base = "{$this->namespace}/{$this->restBase}";
        // Core registers the revision routes under the post type's rest_base
        // or name, not under this controller's rest_base.
        $obj = get_post_type_object(glsr()->post_type);
        $revisionsBase = sprintf('%s/%s/%d/revisions', $this->namespace, $obj->rest_base ?: $obj->name, $review->ID);
        $revisions = wp_get_post_revisions($review->ID, ['fields' => 'ids']);
        $revisionCount = count($revisions);
        $links = [
            'self' => [
                'href' => rest_url(trailingslashit($base).$review->ID),
            ],
            'collection' => [
                'href' => rest_url($base),
            ],
            'about' => [
                'href' => rest_url('wp/v2/types/'.glsr()->post_type),
            ],
            'https://api.w.org/attachment' => [
                'href' => add_query_arg('parent', $review->ID, rest_url('wp/v2/media')),
            ],
            'https://api.w.org/term' => [
                'embeddable' => true,
                'href' => add_query_arg('post', $review->ID, rest_url('wp/v2/'.glsr()->taxonomy)),
                'taxonomy' => glsr()->taxonomy,
            ],
            'version-history' => [
                'count' => $revisionCount,
                'href' => rest_url($revisionsBase),
            ],
        ];
        if ($revisionCount > 0) {
            $lastRevision = array_shift($revisions);
            $links['predecessor-version'] = [
                'href' => rest_url("{$revisionsBase}/{$lastRevision}"),
                'id' => $lastRevision,
            ];
        }
        if (!empty($review->user_id)) {
            $links['author'] = [
                'embeddable' => true,
                'href' => rest_url("wp/v2/users/{$review->user_id}"),
            ];
        }
        if (post_type_supports(glsr()->post_type, 'comments')) {
            $links['replies'] = [
                'embeddable' => true,
                'href' => add_query_arg('post', $review->ID, rest_url('wp/v2/comments')),
            ];
        }
        return $links;
    }
}
