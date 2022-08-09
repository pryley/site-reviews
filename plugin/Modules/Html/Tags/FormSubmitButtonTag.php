<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class FormSubmitButtonTag extends FormTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'class' => glsr(Style::class)->classes('button'),
                'loading_text' => __('Submitting, please wait...', 'site-reviews'),
                'text' => __('Submit your review', 'site-reviews'),
            ],
        ]);
    }
}
