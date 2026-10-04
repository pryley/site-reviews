<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;

class Controller extends AbstractController
{
    /**
     * @action enqueue_block_assets
     */
    public function enqueueCanvasScript(): void
    {
        if (!is_admin()) {
            return;
        }
        $handle = glsr(CompatScript::class)->scriptHandle('public');
        if (wp_script_is($handle, 'registered')) {
            wp_enqueue_script($handle);
        }
        glsr(CompatScript::class)->addToDependents();
    }

    /**
     * @action admin_print_footer_scripts
     * @action wp_print_footer_scripts
     * @action wp_print_scripts
     */
    public function requireCompatScript(): void
    {
        glsr(CompatScript::class)->addToDependents();
    }
}
