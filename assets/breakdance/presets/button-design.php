<?php defined('WPINC') || die;

use function Breakdance\Elements\c;

$controls = c("button", "Button",
    [
        c("alignment", "Alignment",
            [],
            [
                'type' => 'button_bar',
                'layout' => 'vertical',
                'items' => [
                    [
                        'text' => 'Left',
                        'value' => 'flex-start',
                    ],
                    [
                        'text' => 'Center',
                        'value' => 'center',
                    ],
                    [
                        'text' => 'Right',
                        'value' => 'flex-end',
                    ],
                    [
                        'text' => 'Full Width',
                        'value' => 'unset',
                    ],
                ],
            ],
            false,
            false,
            [],
          ),
    ],
    [
        'type' => 'section',
    ],
    false,
    false,
    [],
);

return $controls;
