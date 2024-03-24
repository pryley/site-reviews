<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Modules\Notice;

class RepairReviewRelations extends AbstractCommand
{
    public function handle(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to repair the review relationships.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        glsr(TableRatings::class)->removeInvalidRows();
        glsr(Notice::class)->clear()->addSuccess(
            _x('The review relationships have been repaired.', 'admin-text', 'site-reviews')
        );
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
