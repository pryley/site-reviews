<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Modules\Notice;

class ResetAssignedMeta extends AbstractCommand
{
    public function handle(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to reset the assigned meta values.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        glsr(CountManager::class)->recalculate();
        glsr(Notice::class)->clear()->addSuccess(
            _x('The assigned meta values have been reset.', 'admin-text', 'site-reviews')
        );
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
