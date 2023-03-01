<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\EscapeFormula;
use GeminiLabs\League\Csv\Exceptions\CannotInsertRecord;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Export;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ExportReviews implements Contract
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!glsr()->hasPermission('tools', 'general')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to export reviews.', 'admin-text', 'site-reviews')
            );
            return;
        }
        $reviews = $this->results();
        if (empty($reviews)) {
            glsr(Notice::class)->addWarning(_x('No reviews found.', 'admin-text', 'site-reviews'));
            return;
        }
        try {
            $filename = sprintf('%s_%s.csv', date('YmdHi'), glsr()->id);
            $writer = Writer::createFromString('');
            $writer->addFormatter(new EscapeFormula());
            $writer->insertOne(array_keys($reviews[0]));
            $writer->insertAll($reviews);
            nocache_headers();
            $writer->output($filename);
            exit;
        } catch (CannotInsertRecord $e) {
            glsr(Notice::class)->addError($e->getMessage());
            glsr_log()
                ->warning('Unable to insert row into CSV export file')
                ->debug($e->getRecord());
        }
    }

    /**
     * @return array
     */
    public function results()
    {
        if ('id' === $this->request->assigned_posts) {
            $results = glsr(Export::class)->export($this->request->toArray());
        }
        if ('slug' === $this->request->assigned_posts) {
            $results = glsr(Export::class)->exportWithSlugs($this->request->toArray());
        }
        if (empty($results)) {
            return [];
        }
        return $results;
    }
}
