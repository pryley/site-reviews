<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;

class RegisterTinymcePopups implements Contract
{
    public $popups;

    public function __construct($input)
    {
        $this->popups = $input;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->popups as $slug => $label) {
            $tinymceClass = Helper::buildClassName([$slug, 'tinymce'], 'Tinymce');
            if (!class_exists($tinymceClass)) {
                glsr_log()->error(sprintf('Tinymce Popup class missing (%s)', $tinymceClass));
                continue;
            }
            $tinymce = glsr($tinymceClass)->register($slug, [
                'label' => $label,
                'title' => $label,
            ]);
            glsr()->append('mce', $tinymce->properties, $slug);
        }
    }
}
