<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterTinymcePopups extends AbstractCommand
{
    public $popups;

    public function __construct($input)
    {
        $this->popups = $input;
    }

    public function handle(): void
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
