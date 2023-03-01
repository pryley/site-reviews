<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class ConvertTableEngine implements Contract
{
    public $table;

    public function __construct(Request $request)
    {
        $this->table = $request->table;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to modify the database.', 'admin-text', 'site-reviews'),
            );
            return false;
        }
        $result = glsr(Tables::class)->convertTableEngine($this->table);
        if (-1 === $result) {
            glsr(Notice::class)->addWarning(
                sprintf(_x('The <code>%s</code> table was either not found in the database, or does not use the MyISAM engine.', 'admin-text', 'site-reviews'), $this->table)
            );
            return false;
        }
        if (0 === $result) {
            glsr(Notice::class)->addError(
                sprintf(_x('The <code>%s</code> table could not be converted to InnoDB.', 'admin-text', 'site-reviews'), $this->table)
            );
            return false;
        }
        if (1 === $result) {
            glsr(Notice::class)->addSuccess(
                sprintf(_x('The <code>%s</code> table was successfully converted to InnoDB.', 'admin-text', 'site-reviews'), $this->table)
            );
        }
        return true;
    }
}
