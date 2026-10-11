<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Plugins;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * @see https://nitropack.io/
 */
class Nitropack extends AbstractPlugin
{
    public function isActive(): bool
    {
        if (!function_exists('nitropack_invalidate') || !class_exists('NitroPack\WordPress\Settings\CPTOptimization')) {
            return false;
        }
        return (bool) get_option('nitropack-autoCachePurge', 1);
    }

    public function purgeAll(): void
    {
        nitropack_invalidate(null, null, 'Invalidating all pages after creating/updating/deleting one or more unassigned reviews');
    }

    public function purgePost(int $postId): void
    {
        $post = get_post($postId);
        $postType = $post->post_type ?? 'post';
        if (!in_array($postType, $this->cacheableTypes())) {
            return;
        }
        $postTitle = $post->post_title ?? '';
        nitropack_invalidate(null, "single:{$postId}", sprintf('Invalidating "%s" after creating/updating/deleting an assigned review', $postTitle));
    }

    protected function cacheableTypes(): array
    {
        $optimization = \NitroPack\WordPress\Settings\CPTOptimization::getInstance();
        if (!is_object($optimization)) {
            return [];
        }
        return Arr::consolidate($optimization->nitropack_get_cacheable_object_types());
    }
}
