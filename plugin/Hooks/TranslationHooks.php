<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Modules\Translation;

class TranslationHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function translateAdminEditPage()
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

    /**
     * @return void
     */
    public function translateAdminPostPage()
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            $this->hook(TranslationController::class, [
                ['filterPostStatusLabels', 'gettext_default', 10, 2],
                ['translatePostStatusLabels', 'admin_print_scripts-post.php'],
            ]);
        }
    }

    /**
     * @return void
     */
    public function translatePlugin()
    {
        if (!empty(glsr(Translation::class)->translations())) {
            $this->hook(TranslationController::class, [
                ['filterGettext', "gettext_{$this->id}", 10, 2],
                ['filterGettextWithContext', "gettext_with_context_{$this->id}", 10, 3],
                ['filterNgettext', "ngettext_{$this->id}", 10, 4],
                ['filterNgettextWithContext', "ngettext_with_context_{$this->id}", 10, 5],
            ]);
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        add_action('load-edit.php', [$this, 'translateAdminEditPage']);
        add_action('load-post.php', [$this, 'translateAdminPostPage']);
        add_action('plugins_loaded', [$this, 'translatePlugin']);
    }
}
