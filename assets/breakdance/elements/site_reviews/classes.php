<?php defined('WPINC') || die;

return [
    [
        'name' => 'has-custom-color',
        'template' => '{{ design.general.style_rating_color ? \'true\' }}',
    ],
    [
        'name' => 'has-text-align-center',
        'template' => '{% if design.general.style_text_align == \'center\' %}yes{% endif %}'
    ],
    [
        'name' => 'has-text-align-left',
        'template' => '{% if design.general.style_text_align == \'left\' %}yes{% endif %}'
    ],
    [
        'name' => 'has-text-align-right',
        'template' => '{% if design.general.style_text_align == \'right\' %}yes{% endif %}'
    ],
];
