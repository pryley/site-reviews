<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Modules\Translation;

class TranslationHooks extends AbstractHooks
{
    public function translateAdminEditPage(): void
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            $this->hook(TranslationController::class, [
                ['filterBulkUpdateMessages', 'bulk_post_updated_messages', 10, 2],
                ['filterPostStates', 'display_post_states', 10, 2],
                ['filterPostStatusLabels', 'gettext_default', 10, 2],
                ['filterPostStatusText', 'ngettext_default', 10, 4],
            ]);
        }
    }

    public function translateAdminPostPage(): void
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            $this->hook(TranslationController::class, [
                ['filterPostStatusLabels', 'gettext_default', 10, 2],
                ['translatePostStatusLabels', 'admin_print_scripts-post.php'],
            ]);
        }
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

    public function run(): void
    {
        add_action('load-edit.php', [$this, 'translateAdminEditPage']);
        add_action('load-post.php', [$this, 'translateAdminPostPage']);
        add_action('init', [$this, 'translatePlugin'], -10);
    }
}
