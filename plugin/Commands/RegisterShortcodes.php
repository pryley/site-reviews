<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterShortcodes implements Contract
{
    public $shortcodes;

    public function __construct()
    {
        $this->shortcodes = [
            'site_review',
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ];
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->shortcodes as $shortcode) {
            $shortcodeClass = Helper::buildClassName([$shortcode, 'shortcode'], 'Shortcodes');
            if (!class_exists($shortcodeClass)) {
                glsr_log()->error(sprintf('Shortcode class missing (%s)', $shortcodeClass));
                continue;
            }
            add_shortcode($shortcode, [glsr($shortcodeClass), 'buildShortcode']);
        }
    }
}
