<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Modules\Translation;

class TranslationHooks extends AbstractHooks
{
    public function run(): void
    {
        add_action('after_setup_theme', [$this, 'translatePlugin'], 20);
        $this->hook(TranslationController::class, [
            ['filterBulkUpdateMessages', 'bulk_post_updated_messages', 10, 2],
            ['filterPostStates', 'display_post_states'],
            ['filterPostStatusLabels', 'gettext_default', 10, 2],
            ['translatePostStatusLabels', 'current_screen'],
            ['translatePostStatusLabelsInScripts', 'admin_print_scripts-post.php'],
        ]);
    }

    public function translatePlugin(): void
    {
        if (!empty(glsr(Translation::class)->strings())) {
            $this->hook(TranslationController::class, [
                ['filterGettext', "gettext_{$this->id}", 20, 2],
                ['filterGettextWithContext', "gettext_with_context_{$this->id}", 20, 3],
                ['filterNgettext', "ngettext_{$this->id}", 20, 4],
                ['filterNgettextWithContext', "ngettext_with_context_{$this->id}", 20, 5],
            ]);
        }
    }
}
