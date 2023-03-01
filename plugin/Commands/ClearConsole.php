<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;

class ClearConsole implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        if (!glsr()->hasPermission('tools', 'console')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to clear the console.', 'admin-text', 'site-reviews')
            );
        } else {
            glsr(Console::class)->clear();
            glsr(Notice::class)->addSuccess(_x('Console cleared.', 'admin-text', 'site-reviews'));
        }
    }
}
