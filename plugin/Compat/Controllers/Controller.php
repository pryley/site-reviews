<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
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
     * Convert the attributes of a block that was saved before 8.0.
     *
     * @filter render_block_data
     */
    public function filterBlockData(array $block): array
    {
        $blocks = ['site-reviews/form', 'site-reviews/reviews', 'site-reviews/summary'];
        if (!in_array($block['blockName'] ?? '', $blocks, true)) {
            return $block;
        }
        $attributes = Arr::consolidate($block['attrs'] ?? []);
        $replaced = [
            'assign_to' => 'assigned_posts',
            'assigned_to' => 'assigned_posts',
            'category' => 'assigned_terms',
            'user' => 'assigned_users',
        ];
        foreach ($replaced as $old => $new) {
            if (empty($attributes[$new]) && !empty($attributes[$old])) {
                $attributes[$new] = $attributes[$old];
            }
            unset($attributes[$old]);
        }
        if (is_bool($attributes['schema'] ?? null)) {
            $attributes['schema'] = (int) $attributes['schema']; // the block declares 0 or 1
        }
        $block['attrs'] = $attributes;
        return $block;
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
