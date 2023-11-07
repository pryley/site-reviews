<?php

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePost;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['duplicateReview', 'duplicate_post_post_copy', 10, 2],
            ['filterBulkActions', "bulk_actions-edit-{$this->type}", 100],
            ['filterRowActions', 'post_row_actions', 100, 2],
            ['removeRewriteEditorLink', 'post_submitbox_start', 1],
        ]);
    }
}
