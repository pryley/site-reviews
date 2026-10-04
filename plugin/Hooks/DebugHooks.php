<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\DebugController;

class DebugHooks extends AbstractHooks
{
    public function run(): void
    {
        // each runs after the script that it follows has been enqueued
        $this->hook(DebugController::class, [
            ['enqueueAdminScript', 'admin_enqueue_scripts', 20],
            ['enqueueCanvasScript', 'enqueue_block_assets', 999],
            ['enqueuePublicScript', 'enqueue_block_editor_assets', 20],
            ['enqueuePublicScript', 'wp_enqueue_scripts', 1000],
        ]);
    }
}
