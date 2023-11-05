<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterBlocks extends AbstractCommand
{
    public $blocks;

    public function __construct($input)
    {
        $this->blocks = $input;
    }

    public function handle(): void
    {
        foreach ($this->blocks as $block) {
            $blockClass = Helper::buildClassName([$block, 'block'], 'Blocks');
            if (!class_exists($blockClass)) {
                glsr_log()->error(sprintf('Block class missing (%s)', $blockClass));
                continue;
            }
            glsr($blockClass)->register(
                str_replace(['site_reviews_', 'site_'], '', $block)
            );
        }
    }
}
