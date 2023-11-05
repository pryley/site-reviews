<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

class ImportRatings extends AbstractCommand
{
    public const PER_PAGE = 250;

    public function handle(): void
    {
        $this->import();
        $this->cleanup();
    }

    protected function cleanup(): void
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
        glsr(Migrate::class)->reset();
    }

    protected function import(): void
    {
        $page = 0;
        while (true) {
            $values = glsr(Query::class)->import([
                'page' => $page,
                'per_page' => static::PER_PAGE,
            ]);
            if (empty($values)) {
                break;
            }
            $this->importRatings($values);
            $this->importAssignedPosts($values);
            $this->importAssignedUsers($values);
            // It is unecessary to import term assignments as this is done in Migration
            ++$page;
        }
    }

    protected function importAssignedPosts(array $values): void
    {
        if ($values = $this->prepareAssignedValues($values, 'post')) {
            glsr(Database::class)->insertBulk('assigned_posts', $values, [
                'rating_id',
                'post_id',
                'is_published',
            ]);
        }
    }

    protected function importAssignedUsers(array $values): void
    {
        if ($values = $this->prepareAssignedValues($values, 'user')) {
            glsr(Database::class)->insertBulk('assigned_users', $values, [
                'rating_id',
                'user_id',
            ]);
        }
    }

    protected function importRatings(array $values): void
    {
        array_walk($values, [$this, 'prepareRating']);
        $fields = array_keys(glsr(RatingDefaults::class)->unguardedDefaults());
        glsr(Database::class)->insertBulk('ratings', $values, $fields);
    }

    protected function prepareAssignedValues(array $results, string $key): array
    {
        $assignedKey = $key.'_id';
        $values = [];
        foreach ($results as $result) {
            $meta = maybe_unserialize($result['meta_value']);
            if (!$assignedIds = Arr::uniqueInt(Arr::getAs('array', $meta, "{$key}_ids"))) {
                continue;
            }
            foreach ($assignedIds as $assignedId) {
                $value = [
                    'rating_id' => Arr::getAs('int', $meta, 'ID'),
                    $assignedKey => $assignedId,
                ];
                if ('post' === $key) {
                    $value['is_published'] = Arr::getAs('bool', $meta, 'is_approved');
                }
                $values[] = $value;
            }
        }
        return $values;
    }

    protected function prepareRating(array &$result): void
    {
        $values = maybe_unserialize($result['meta_value']);
        $values['review_id'] = $result['post_id'];
        $result = glsr(RatingDefaults::class)->unguardedRestrict($values);
    }
}
