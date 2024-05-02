<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\BlocksController;

class BlocksHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(BlocksController::class, [
            ['filterAllowedBlockTypes', 'allowed_block_types_all', 10, 2],
            ['filterBlockCategories', 'block_categories_all'],
            ['filterUseBlockEditor', 'use_block_editor_for_post_type', 10, 2],
            ['registerAssets', 'init', 9], // This must be done before the blocks are registered
            ['registerBlocks', 'init'],
            ['removeLegacyWidgets', 'widget_types_to_hide_from_legacy_widget_block'],
        ]);
    }
}
