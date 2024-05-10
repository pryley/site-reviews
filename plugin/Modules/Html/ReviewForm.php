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
        $class = $args['class'] ?? ''; // keep the class option from the review form settings
        $overrides = [
            'button_text' => __('Submit Review', 'site-reviews'),
            'class' => "{$class} glsr-review-form",
        ];
        parent::__construct(wp_parse_args($overrides, $args), $values);
        glsr()->action('review-form', $this);
    }

    public function config(): array
    {
        $config = glsr()->config('forms/review-form');
        $config = glsr()->filterArray('review-form/fields', $config, $this);
        return $config;
    }

    public function field(string $name, array $args): FieldContract
    {
        $field = new ReviewField(wp_parse_args($args, compact('name')));
        $this->normalizeField($field);
        return $field;
    }

    public function loadSession(array $values): void
    {
        $this->session = glsr()->args([
            'errors' => Arr::consolidate(glsr()->sessionGet('form_errors', [])),
            'message' => Cast::toString(glsr()->sessionGet('form_message', '')),
            'values' => $values ?: Arr::consolidate(glsr()->sessionGet('form_values', [])),
        ]);
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsAll(): array
    {
        $fields = parent::fieldsAll();
        $fields = glsr()->filterArray('review-form/fields/all', $fields, $this);
        return $fields;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsHidden(): array
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
            'excluded' => glsr(Encryption::class)->encrypt($this->args->cast('hide', 'string')),
            'form_id' => $this->args->id,
            'terms_exist' => Cast::toInt(!in_array('terms', $this->args->cast('hide', 'array'))),
        ];
        $fields = [];
        foreach ($config as $name => $value) {
            $field = $this->field($name, [
                'type' => 'hidden',
                'value' => $value,
            ]);
            if ($field->isValid()) {
                $fields[$name] = $field;
            }
        }
        $fields = glsr()->filterArray('review-form/fields/hidden', $fields, $this);
        return $fields;
    }

    /**
     * @return FieldContract[]
     */
    protected function fieldsVisible(): array
    {
        $fields = parent::fieldsVisible();
        $fields = array_filter($fields, fn ($field) => !in_array($field->original_name, $this->args->cast('hide', 'array')));
        $fields = glsr()->filterArray('review-form/fields/visible', $fields, $this);
        return $fields;
    }
}
