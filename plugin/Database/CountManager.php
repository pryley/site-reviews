<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;

class CountManager
{
    public const META_AVERAGE = '_glsr_average';
    public const META_RANKING = '_glsr_ranking';
    public const META_REVIEWS = '_glsr_reviews';

    public function posts(int $postId): void
    {
        $counts = glsr_get_ratings(['assigned_posts' => $postId]);
        update_post_meta($postId, static::META_AVERAGE, $counts->average);
        update_post_meta($postId, static::META_RANKING, $counts->ranking);
        update_post_meta($postId, static::META_REVIEWS, $counts->reviews);
    }

    public function recalculate(): void
    {
        $this->recalculateFor('post');
        $this->recalculateFor('term');
        $this->recalculateFor('user');
    }

    public function recalculateFor(string $metaGroup): void
    {
        $metaGroup = strtolower(Str::restrictTo(['post', 'term', 'user'], $metaGroup, 'post'));
        $metaKeys = [static::META_AVERAGE, static::META_RANKING, static::META_REVIEWS];
        $metaTable = $this->metaTable($metaGroup);
        glsr(Database::class)->deleteMeta($metaKeys, $metaTable);
        if ($values = $this->ratingValuesForInsert($metaGroup)) {
            glsr(Database::class)->insertBulk($metaTable, $values, [
                $this->metaId($metaGroup),
                'meta_key',
                'meta_value',
            ]);
        }
    }

    public function terms(int $termId): void
    {
        $counts = glsr_get_ratings(['assigned_terms' => $termId]);
        update_term_meta($termId, static::META_AVERAGE, $counts->average);
        update_term_meta($termId, static::META_RANKING, $counts->ranking);
        update_term_meta($termId, static::META_REVIEWS, $counts->reviews);
    }

    public function users(int $userId): void
    {
        $counts = glsr_get_ratings(['assigned_users' => $userId]);
        update_user_meta($userId, static::META_AVERAGE, $counts->average);
        update_user_meta($userId, static::META_RANKING, $counts->ranking);
        update_user_meta($userId, static::META_REVIEWS, $counts->reviews);
    }

    protected function metaId(string $metaGroup): string
    {
        return sprintf('%s_id', $metaGroup);
    }

    protected function metaTable(string $metaGroup): string
    {
        return sprintf('%smeta', $metaGroup);
    }

    protected function ratingValuesForInsert(string $metaGroup): array
    {
        $metaId = $this->metaId($metaGroup);
        $ratings = glsr(RatingManager::class)->ratingsGroupedBy($metaGroup);
        $values = [];
        foreach ($ratings as $id => $counts) {
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_AVERAGE,
                'meta_value' => glsr(Rating::class)->average($counts),
            ];
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_RANKING,
                'meta_value' => glsr(Rating::class)->ranking($counts),
            ];
            $values[] = [
                $metaId => $id,
                'meta_key' => static::META_REVIEWS,
                'meta_value' => array_sum($counts),
            ];
        }
        return $values;
    }
}
