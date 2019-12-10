<?php

return [
    'fields' => [
        'input' => 'wpcf7-form-control',
        'input_text' => 'wpcf7-form-control wpcf7-text',
        'input_email' => 'wpcf7-form-control wpcf7-text wpcf7-email',
        'select' => 'wpcf7-form-control wpcf7-select',
        'textarea' => 'wpcf7-form-control wpcf7-textarea',
    ],
    'validation' => [
        'error_tag' => 'span',
        'error_tag_class' => 'wpcf7-not-valid-tip',
        'input_error_class' => 'wpcf7-not-valid',
        'message_initial_class' => 'wpcf7-display-none',
        'message_success_class' => 'wpcf7-mail-sent-ok',
        'message_tag_class' => 'wpcf7-response-output',
    ],
];
