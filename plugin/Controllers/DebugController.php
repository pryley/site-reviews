<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Modules\Assets\DebugScript;

class DebugController extends AbstractController
{
    /**
     * @action admin_enqueue_scripts
     */
    public function enqueueAdminScript(): void
    {
        if (wp_script_is(glsr()->id.'/admin', 'enqueued')) {
            $this->enqueue('admin');
        }
    }

    /**
     * @action enqueue_block_assets
     */
    public function enqueueCanvasScript(): void
    {
        if (is_admin() && wp_script_is(glsr()->id, 'registered')) {
            $this->enqueue('public');
        }
    }

    /**
     * @action enqueue_block_editor_assets
     * @action wp_enqueue_scripts
     */
    public function enqueuePublicScript(): void
    {
        if (wp_script_is(glsr()->id, 'enqueued')) {
            $this->enqueue('public');
        }
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    protected function enqueue(string $bundle): void
    {
        $debug = glsr(DebugScript::class);
        if (!$debug->isEnabled($bundle)) {
            return;
        }
        if ('admin' === $bundle) {
            wp_enqueue_script($debug->scriptHandle('admin'), $debug->url('admin'), [glsr()->id.'/admin'], glsr()->version, [
                'strategy' => 'defer',
            ]);
            return;
        }
        wp_enqueue_script($debug->scriptHandle('public'), $debug->url('public'), [glsr()->id], glsr()->version, [
            'in_footer' => true,
            'strategy' => 'defer',
        ]);
    }
}
