<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class FormResponseTag extends FormTag
{
    /**
     * @return string|void
     */
    protected function contextClass()
    {
        if (!empty($this->with->errors)) {
            $defaults = glsr(StyleValidationDefaults::class)->defaults();
            return Arr::get($defaults, 'message_error_class');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return glsr(Template::class)->build('templates/form/response', [
            'context' => [
                'class' => $this->contextClass(),
                'message' => wpautop($this->with->message),
            ],
            'has_errors' => !empty($this->with->errors),
        ]);
    }
}
