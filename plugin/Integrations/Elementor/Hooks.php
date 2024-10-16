<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

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
            ['filterElementorPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after', 1],
            ['filterElementorStarRatingDefaults', 'site-reviews/defaults/star-rating/defaults'],
            ['filterGeneratedSchema', 'site-reviews/schema/generate'],
            ['parseElementCss', 'elementor/element/parse_css', 10, 2],
            ['registerElementorCategory', 'elementor/elements/categories_registered'],
            ['registerElementorWidgets', 'elementor/widgets/register'],
            ['registerInlineStyles', 'elementor/editor/after_enqueue_styles'],
            ['registerInlineStyles', 'elementor/preview/enqueue_styles'],
            ['registerScripts', 'elementor/editor/after_enqueue_scripts'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('Elementor\Plugin');
    }

    protected function isVersionSupported(): bool
    {
        return defined('ELEMENTOR_VERSION') && version_compare(\ELEMENTOR_VERSION, '3.19.0', '>=');
    }

    protected function unsupportedVersionNotice(): void
    {
        add_action('admin_notices', function () {
            if (!str_starts_with(glsr_current_screen()->post_type, glsr()->post_type)) {
                return;
            }
            glsr(Notice::class)->addWarning(
                _x('Update Elementor to v3.19.0 or higher to enable integration with Site Reviews.', 'admin-text', 'site-reviews')
            );
        });
    }
}
