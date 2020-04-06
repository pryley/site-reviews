<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field as HtmlField;

class Honeypot extends Field
{
    /**
     * @inheritDoc
     */
    public function getArgs()
    {
        $honeypotArgs = apply_filters('site-reviews/field/honeypot/args', [
            'class' => 'glsr-field-control',
            'label' => esc_html__('Your review', 'site-reviews'),
            'name' => strtolower(Str::random()),
            'required' => true,
            'type' => 'text',
        ]);
        $field = new HtmlField($honeypotArgs);
        $field->field['id'] .= '-'.Arr::get($this->builder->args, 'suffix');
        $this->builder->args['text'] = $field->getFieldLabel().$field->getField();
        unset($this->builder->args['suffix']);
        return $this->builder->args;
    }

    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'div';
    }

    /**
     * @inheritDoc
     */
    public static function required()
    {
        return [
            'class' => 'glsr-field glsr-required',
            'is_raw' => true,
            'style' => 'display:none;'
        ];
    }
}
