<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ConvertTableEngine extends AbstractCommand
{
    /** @var string */
    public $table;

    public function __construct(Request $request)
    {
        $this->table = $request->table;
    }

    public function handle(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to modify the database.', 'admin-text', 'site-reviews'),
            );
            $this->fail();
            return;
        }
        $result = glsr(Tables::class)->convertTableEngine($this->table);
        if (-1 === $result) {
            glsr(Notice::class)->addWarning(
                sprintf(_x('The <code>%s</code> table was either not found in the database, or does not use the MyISAM engine.', 'admin-text', 'site-reviews'), $this->table)
            );
            $this->fail();
            return;
        }
        if (0 === $result) {
            glsr(Notice::class)->addError(
                sprintf(_x('The <code>%s</code> table could not be converted to InnoDB.', 'admin-text', 'site-reviews'), $this->table)
            );
            $this->fail();
            return;
        }
        if (1 === $result) {
            glsr(Notice::class)->addSuccess(
                sprintf(_x('The <code>%s</code> table was successly converted to InnoDB.', 'admin-text', 'site-reviews'), $this->table)
            );
        }
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
