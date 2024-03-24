<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Helpers\Cast;

class ReviewForm extends Form
{
    public function __construct(array $args = [], array $requiredKeys = [])
    {
        $overrides = [
            'button_text' => __('Submit Review', 'site-reviews'),
            'class' => 'glsr-review-form',
        ];
        if (empty($requiredKeys)) {
            $requiredKeys = glsr_get_option('forms.required', []);
        }
        parent::__construct(wp_parse_args($overrides, $args), $requiredKeys);
    }

    public function field(string $name, array $args): FieldContract
    {
        $field = new ReviewField(wp_parse_args($args, compact('name')));
        $this->normalizeField($field);
        return $field;
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
            'excluded' => $this->args->hide,
            'form_id' => $this->args->id,
            'terms_exist' => Cast::toInt(!in_array('terms', $this->args->cast('hide', 'array'))),
        ];
        $fields = [];
        foreach ($config as $key => $value) {
            $field = $this->field($key, [
                'type' => 'hidden',
                'value' => $value,
            ]);
            if ($field->isValid()) {
                $fields[$key] = $field;
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
        $config = glsr()->config('forms/review-form');
        $config = glsr()->filterArray('review-form/fields', $config, $this);
        $fields = [];
        foreach ($config as $key => $args) {
            $field = $this->field($key, $args);
            if ($field->isValid()) {
                $fields[$key] = $field;
            }
        }
        $fields = glsr()->filterArray('review-form/fields/visible', $fields, $this);
        return $fields;
    }
}
