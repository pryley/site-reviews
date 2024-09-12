<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ImportManager;
use GeminiLabs\SiteReviews\Defaults\ImportResultDefaults;
use GeminiLabs\SiteReviews\Request;

class ImportReviewsAttachments extends AbstractCommand
{
    protected int $limit;
    protected int $offset;
    protected array $importResult;

    public function __construct(Request $request)
    {
        $this->limit = max(1, $request->cast('per_page', 'int'));
        $this->offset = $this->limit * (max(1, $request->cast('page', 'int')) - 1);
        $this->importResult = glsr(ImportResultDefaults::class)->defaults();
    }

    public function handle(): void
    {
        $this->importResult = glsr(ImportManager::class)->importAttachments($this->limit, $this->offset);
    }

    public function response(): array
    {
        return wp_parse_args($this->importResult, [
            'message' => _x('Processed %d attachments', 'admin-text', 'site-reviews'),
        ]);
    }
}
