<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Modules\Notice;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->unsupportedVersionNotice();
            return;
        }
        $this->hook(Controller::class, [
            ['filterButtonClass', 'site-reviews/defaults/style-classes/defaults'],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
            ['onActivated', 'site-reviews/activated'],
            ['registerFusionElements', 'fusion_builder_before_init'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('FusionBuilder')
            && class_exists('Fusion_Element')
            && function_exists('fusion_builder_auto_activate_element')
            && function_exists('fusion_builder_frontend_data')
            && function_exists('fusion_builder_map');
    }

    protected function isVersionSupported(): bool
    {
        return defined('FUSION_BUILDER_VERSION') && version_compare(\FUSION_BUILDER_VERSION, '3.11.0', '>=');
    }

    protected function unsupportedVersionNotice(): void
    {
        add_action('admin_notices', function () {
            if (!str_starts_with(glsr_current_screen()->post_type, glsr()->post_type)) {
                return;
            }
            glsr(Notice::class)->addWarning(
                _x('Update Fusion Builder to v3.11.0 or higher to enable integration with Site Reviews.', 'admin-text', 'site-reviews')
            );
        });
    }
}
