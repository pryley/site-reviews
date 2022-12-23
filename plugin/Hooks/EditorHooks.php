<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\EditorController;

class EditorHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(EditorController::class, [
            ['filterEditorSettings', 'wp_editor_settings'],
            ['filterEditorTextarea', 'the_editor'],
            ['filterIsProtectedMeta', 'is_protected_meta', 10, 3],
            ['filterUpdateMessages', 'post_updated_messages'],
            ['mceShortcodeAjax', 'site-reviews/route/ajax/mce-shortcode'],
            ['renderReviewNotice', 'edit_form_top'],
        ]);
    }
}
