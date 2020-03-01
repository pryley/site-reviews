<?php

return [
    'rating' => [
        'label' => esc_html__('Your overall rating', 'site-reviews'),
        'type' => 'rating',
    ],
    'title' => [
        'label' => esc_html__('Title of your review', 'site-reviews'),
        'placeholder' => esc_attr__('Summarize your review or highlight an interesting detail', 'site-reviews'),
        'type' => 'text',
    ],
    'content' => [
        'label' => esc_html__('Your review', 'site-reviews'),
        'placeholder' => esc_attr__('Tell people your review', 'site-reviews'),
        'rows' => 5,
        'type' => 'textarea',
    ],
    'name' => [
        'label' => esc_html__('Your name', 'site-reviews'),
        'placeholder' => esc_attr__('Tell us your name', 'site-reviews'),
        'type' => 'text',
    ],
    'email' => [
        'label' => esc_html__('Your email', 'site-reviews'),
        'placeholder' => esc_attr__('Tell us your email', 'site-reviews'),
        'type' => 'email',
    ],
    'terms' => [
        'label' => esc_html__('This review is based on my own experience and is my genuine opinion.', 'site-reviews'),
        'type' => 'checkbox',
    ],
];
