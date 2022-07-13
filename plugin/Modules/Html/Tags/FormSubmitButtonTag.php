<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Template;

class FormSubmitButtonTag extends FormTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'text' => __('Submit your review', 'site-reviews'),
            ],
        ]);
    }
}
