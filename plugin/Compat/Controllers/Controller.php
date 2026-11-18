<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
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
     * Read the assignments of a block from the attributes that 8.0 replaced.
     *
     * @filter site-reviews/shortcode/args
     */
    public function filterBlockAssignments(array $args, ShortcodeContract $shortcode): array
    {
        if ('block' !== $shortcode->from) {
            return $args;
        }
        $replaced = [
            'assign_to' => 'assigned_posts',
            'assigned_to' => 'assigned_posts',
            'category' => 'assigned_terms',
            'user' => 'assigned_users',
        ];
        foreach ($replaced as $old => $new) {
            if (empty($args[$new]) && !empty($args[$old])) {
                $args[$new] = $args[$old];
            }
            unset($args[$old]);
        }
        return $args;
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
