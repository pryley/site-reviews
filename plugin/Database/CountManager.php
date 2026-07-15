<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
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
        glsr()->action('ratings/count/post', $postId, $counts);
    }

    public function postsAverage(int $postId): float
    {
        return Cast::toFloat(get_post_meta($postId, static::META_AVERAGE, true));
    }

    public function postsRanking(int $postId): float
    {
        return Cast::toFloat(get_post_meta($postId, static::META_RANKING, true));
    }

    public function postsReviews(int $postId): int
    {
        return Cast::toInt(get_post_meta($postId, static::META_REVIEWS, true));
    }

    public function recalculate(): void
    {
        $this->recalculateFor('post');
        $this->recalculateFor('term');
        $this->recalculateFor('user');
        glsr()->action('ratings/count/all');
    }

    public function recalculateFor(string $metaGroup): void
    {
        $group = strtolower(Str::restrictTo(['post', 'term', 'user'], $metaGroup, 'post'));
        $metaId = $this->metaId($group);
        $metaKeys = [static::META_AVERAGE, static::META_RANKING, static::META_REVIEWS];
        $metaTable = $this->metaTable($group);
        $staleIds = $this->idsWithRatingMeta($group); // before the rows are deleted
        glsr(Database::class)->deleteMeta($metaKeys, $metaTable);
        if ($values = $this->ratingValuesForInsert($group)) {
            glsr(Database::class)->insertBulk($metaTable, $values, [
                $metaId,
                'meta_key',
                'meta_value',
            ]);
            $staleIds = array_merge($staleIds, array_column($values, $metaId));
        }
        $this->flushMetaCache($group, $staleIds);
    }

    public function terms(int $termId): void
    {
        $counts = glsr_get_ratings(['assigned_terms' => $termId]);
        update_term_meta($termId, static::META_AVERAGE, $counts->average);
        update_term_meta($termId, static::META_RANKING, $counts->ranking);
        update_term_meta($termId, static::META_REVIEWS, $counts->reviews);
        glsr()->action('ratings/count/term', $termId, $counts);
    }

    public function termsAverage(int $termId): float
    {
        return Cast::toFloat(get_term_meta($termId, static::META_AVERAGE, true));
    }

    public function termsRanking(int $termId): float
    {
        return Cast::toFloat(get_term_meta($termId, static::META_RANKING, true));
    }

    public function termsReviews(int $termId): int
    {
        return Cast::toInt(get_term_meta($termId, static::META_REVIEWS, true));
    }

    public function users(int $userId): void
    {
        $counts = glsr_get_ratings(['assigned_users' => $userId]);
        update_user_meta($userId, static::META_AVERAGE, $counts->average);
        update_user_meta($userId, static::META_RANKING, $counts->ranking);
        update_user_meta($userId, static::META_REVIEWS, $counts->reviews);
        glsr()->action('ratings/count/user', $userId, $counts);
    }

    public function usersAverage(int $userId): float
    {
        return Cast::toFloat(get_user_meta($userId, static::META_AVERAGE, true));
    }

    public function usersRanking(int $userId): float
    {
        return Cast::toFloat(get_user_meta($userId, static::META_RANKING, true));
    }

    public function usersReviews(int $userId): int
    {
        return Cast::toInt(get_user_meta($userId, static::META_REVIEWS, true));
    }

    /**
     * The rows are written with raw SQL which leaves WordPress's metadata cache
     * holding the old counts. The meta cache groups are persistent, so on a site
     * with an external object cache the stale counts would outlive the request
     * and the recalculation would appear to have done nothing at all.
     *
     * @param int[] $ids
     */
    protected function flushMetaCache(string $metaGroup, array $ids): void
    {
        $ids = Arr::uniqueInt($ids);
        if (empty($ids)) {
            return;
        }
        wp_cache_delete_multiple($ids, "{$metaGroup}_meta");
    }

    /**
     * The IDs that currently hold a rating meta value. They are needed before the rows
     * are deleted: an ID that loses its last review no longer appears in the values
     * being inserted, but its cached meta is just as stale.
     *
     * @return int[]
     */
    protected function idsWithRatingMeta(string $metaGroup): array
    {
        $metaKeys = glsr(Query::class)->escValuesForInsert([
            static::META_AVERAGE,
            static::META_RANKING,
            static::META_REVIEWS,
        ]);
        $metaId = $this->metaId($metaGroup);
        $metaTable = $this->metaTable($metaGroup);
        return Arr::uniqueInt( // because the SQL statement can be filtered with hook
            glsr(Database::class)->dbGetCol(glsr(Query::class)->sql("
                SELECT DISTINCT {$metaId}
                FROM table|{$metaTable}
                WHERE meta_key IN {$metaKeys}
            "))
        );
    }

    protected function metaId(string $metaGroup): string
    {
        return "{$metaGroup}_id";
    }

    protected function metaTable(string $metaGroup): string
    {
        return "{$metaGroup}meta";
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
