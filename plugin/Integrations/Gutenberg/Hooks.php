<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['enqueueBlockEditorAssets', 'enqueue_block_editor_assets'],
            ['filterAllowedBlockTypes', 'allowed_block_types_all', 10, 2],
            ['filterBlockCategories', 'block_categories_all'],
            ['filterBlockGeneratedClassname', 'block_default_classname', 10, 2],
            ['filterUseBlockEditor', 'use_block_editor_for_post_type', 10, 2],
            ['registerBlocks', 'init'],
            ['removeLegacyWidgets', 'widget_types_to_hide_from_legacy_widget_block'],
        ]);
    }
}
