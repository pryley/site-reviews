<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Queue;

class ExportRatings extends AbstractCommand
{
    protected const PER_PAGE = 250;

    public function __construct(Arguments $args)
    {
        if (glsr()->post_type === $args->content) {
            add_filter('wxr_export_skip_postmeta', [$this, 'filterExportSkipPostMeta'], 10, 2);
        }
    }

    /**
     * @param bool   $skip
     * @param string $metaKey
     *
     * @filter wxr_export_skip_postmeta
     */
    public function filterExportSkipPostMeta($skip, $metaKey): bool
    {
        return !Str::startsWith(Cast::toString($metaKey), ['_custom', '_'.glsr()->prefix]);
    }

    public function handle(): void
    {
        glsr(Database::class)->deleteMeta(glsr()->export_key);
        $this->export();
        $this->cleanup();
    }

    protected function cleanup(): void
    {
        $timestamp = time() + (10 * MINUTE_IN_SECONDS);
        glsr(Queue::class)->once($timestamp, 'queue/export/cleanup');
    }

    protected function export(): void
    {
        $page = 1;
        while (true) {
            $values = glsr(Query::class)->export([
                'page' => $page,
                'per_page' => static::PER_PAGE,
            ]);
            if (empty($values)) {
                break;
            }
            array_walk($values, [$this, 'prepareMeta']);
            glsr(Database::class)->insertBulk('postmeta', $values, [
                'post_id',
                'meta_key',
                'meta_value',
            ]);
            ++$page;
        }
    }

    protected function prepareMeta(array &$result): void
    {
        $postId = $result['review_id'];
        unset($result['review_id']);
        $result = [
            'post_id' => $postId,
            'meta_key' => glsr()->export_key,
            'meta_value' => maybe_serialize($result),
        ];
    }
}
