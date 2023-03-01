<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Modules\Notice;

class ResetAssignedMeta implements Contract
{
    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to reset the assigned meta values.', 'admin-text', 'site-reviews')
            );
            return false;
        }
        glsr(CountManager::class)->recalculate();
        glsr(Notice::class)->clear()->addSuccess(
            _x('The assigned meta values have been reset.', 'admin-text', 'site-reviews')
        );
        return true;
    }
}
