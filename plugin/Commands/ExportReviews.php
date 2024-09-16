<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\CannotInsertRecord;
use GeminiLabs\League\Csv\EscapeFormula;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database\ExportManager;
use GeminiLabs\SiteReviews\Defaults\AdditionalFieldsDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ExportReviews extends AbstractCommand
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(): void
    {
        if (!glsr()->hasPermission('tools', 'general')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to export reviews.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        try {
            $records = $this->fetchReviews();
            $firstRecord = $records->current();
            if (empty($firstRecord)) {
                glsr(Notice::class)->addWarning(_x('No reviews were found to export.', 'admin-text', 'site-reviews'));
                $this->fail();
                return;
            }
            nocache_headers();
            $filename = sprintf('%s_%s.csv', date('YmdHi'), glsr()->id);
            $writer = Writer::createFromString('');
            $writer->addFormatter(new EscapeFormula());
            $writer->insertOne(array_keys($firstRecord));
            $writer->insertAll($records);
            $writer->output($filename);
            exit;
        } catch (CannotInsertRecord $e) {
            $this->fail();
            glsr(Notice::class)->addError($e->getMessage());
            glsr_log()
                ->warning('Unable to insert row into CSV export file')
                ->debug($e->getRecord());
        }
    }

    protected function fetchReviews(): \Generator
    {
        $args = glsr()->args([
            'assigned_posts' => $this->request->assigned_posts,
            'date' => $this->request->cast('date', 'date'),
            'limit' => 500,
            'post_status' => $this->request->post_status,
        ]);
        $header = [];
        $postId = 0;
        while (true) {
            $reviews = glsr(ExportManager::class)->export($args->merge([
                'post_id' => $postId,
            ]));
            if (empty($reviews)) {
                break;
            }
            if (empty($header)) {
                $header = $this->headerValues($args, $reviews[0]);
            }
            foreach ($reviews as $review) {
                $meta = $this->postMeta($review['ID']);
                $custom = wp_parse_args($meta, array_fill_keys($header, ''));
                $record = wp_parse_args($review, $custom);
                unset($record['ID']);
                yield $record;
            }
            $postId = end($reviews)['ID'];
        }
    }

    protected function headerValues(Arguments $args, array $record): array
    {
        $additionalHeader = array_keys(glsr(AdditionalFieldsDefaults::class)->defaults());
        $customHeader = glsr(ExportManager::class)->customHeader($args);
        $header = array_merge(array_keys($record), $additionalHeader, $customHeader);
        return $header;
    }

    protected function postMeta(int $postId): array
    {
        $meta = get_post_meta($postId);
        if (!is_array($meta)) {
            return [];
        }
        $additionalKeys = array_keys(glsr(AdditionalFieldsDefaults::class)->defaults());
        $additionalKeys = array_map(fn ($key) => Str::prefix($key, '_'), $additionalKeys);
        $meta = array_map(fn ($val) => $val[0] ?? '', $meta);
        $meta = array_filter($meta, function ($key) use ($additionalKeys) {
            return str_starts_with($key, '_custom_') || in_array($key, $additionalKeys);
        }, ARRAY_FILTER_USE_KEY);
        $results = [];
        foreach ($meta as $key => $value) {
            $key = Str::removePrefix($key, '_');
            $results[$key] = $value;
        }
        return $results;
    }
}
