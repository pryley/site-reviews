<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\RevisionController;

class RevisionHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(RevisionController::class, [
            ['filterCheckForChanges', 'wp_save_post_revision_check_for_changes', 99, 3],
            ['filterReviewHasChanged', 'wp_save_post_revision_post_has_changed', 10, 3],
            ['filterRevisionUiDiff', 'wp_get_revision_ui_diff', 10, 3],
            ['restoreRevision', 'wp_restore_post_revision', 10, 2],
            ['saveRevision', '_wp_put_post_revision'],
        ]);
    }
}
