<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterBlocks implements Contract
{
    public $blocks;

    public function __construct($input)
    {
        $this->blocks = $input;
    }

    /**
     * @return void
     */
    public function handle()
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
