<?php defined('WPINC') || die;

return [
    [
        'name' => 'has-custom-color',
        'template' => '{{ design.general.style_rating_color ? \'true\' }}',
    ],
    [
        'name' => 'items-justified-center',
        'template' => '{% if design.general.style_align == \'center\' %}yes{% endif %}'
    ],
    [
        'name' => 'items-justified-left',
        'template' => '{% if design.general.style_align == \'left\' %}yes{% endif %}'
    ],
    [
        'name' => 'items-justified-right',
        'template' => '{% if design.general.style_align == \'right\' %}yes{% endif %}'
    ],
];
