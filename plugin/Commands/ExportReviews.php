<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\Exceptions\CannotInsertRecord;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Export;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ExportReviews implements Contract
{
    /**
     * @var string
     */
    protected $assigned_posts;

    public function __construct(Request $request)
    {
        $this->assigned_posts = $request->assigned_posts;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if ('slug' === $this->assigned_posts) {
            $results = glsr(Export::class)->export();
        } else {
            $results = glsr(Export::class)->exportWithIds();
        }
        try {
            $writer = Writer::createFromString('');
            $writer->insertAll($results);
        } catch (CannotInsertRecord $e) {
            glsr(Notice::class)->addError($e->getMessage());
            glsr_log()
                ->warning('Unable to insert row into CSV export file')
                ->debug($e->getRecord());
            return;
        }
        $filename = sprintf('%s_%s.csv', date('YmdHi'), glsr()->id);
        nocache_headers();
        header('Content-Type: text/csv');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo $writer->toString();
        exit;
    }
}
