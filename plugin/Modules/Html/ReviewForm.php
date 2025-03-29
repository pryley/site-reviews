<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Encryption;

class ReviewForm extends Form
{
    public function __construct(array $args = [], array $values = [])
    {
        $overrides = [
            'button_text' => __('Submit Review', 'site-reviews'),
            'class' => 'glsr-review-form',
        ];
        parent::__construct(wp_parse_args($overrides, $args), $values);
    }

    public function config(): array
    {
        return glsr()->config('forms/review-form');
    }

    public function configHidden(): array
    {
        do_action('litespeed_nonce', 'submit-review'); // @litespeedcache
        $referer = glsr()->filterString('review-form/referer', wp_get_referer());
        $config = [
            '_action' => 'submit-review',
            '_nonce' => wp_create_nonce('submit-review'),
            '_post_id' => get_the_ID(),
            '_referer' => wp_unslash($referer),
            'assigned_posts' => $this->args->assigned_posts,
            'assigned_terms' => $this->args->assigned_terms,
            'assigned_users' => $this->args->assigned_users,
            'excluded' => $this->args->cast('hide', 'string'),
            'form_id' => $this->args->id,
            'terms_exist' => Cast::toInt(!in_array('terms', $this->args->cast('hide', 'array'))),
        ];
        return $config;
    }

    public function fieldClass(): string
    {
        return ReviewField::class;
    }

    public function loadSession(array $values): void
    {
        $this->session = glsr()->args([
            'errors' => glsr()->session()->array('form_errors'),
            'failed' => glsr()->session()->cast('form_invalid', 'bool'),
            'message' => glsr()->session()->cast('form_message', 'string'),
            'values' => $values ?: glsr()->session()->array('form_values'),
        ]);
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsVisible(): array
    {
        $fields = parent::fieldsVisible();
        $fields = array_filter($fields, fn ($field) => !in_array($field->original_name, $this->args->cast('hide', 'array')));
        return $fields;
    }
}
