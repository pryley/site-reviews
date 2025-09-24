<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use ET\Builder\Framework\Controllers\RESTController as RESTControllerBase;
use ET\Builder\Framework\UserRole\UserRole;

class RESTController extends RESTControllerBase
{
    public static function options(\WP_REST_Request $request): \WP_REST_Response
    {
        $option = $request->get_param('option');
        $shortcode = $request->get_param('shortcode');

        // get shortcode defaults

        $response = [];
        return static::response_success($response);
    }

    public static function optionsArgs(): array
    {
        return [
            'option' => [
                'type' => 'string',
                'default' => '',
                'validate_callback' => fn ($param) => is_string($param),
            ],
            'shortcode' => [
                'type' => 'string',
                'default' => '',
                'validate_callback' => fn ($param) => is_string($param),
            ],
        ];
    }

    public static function render(\WP_REST_Request $request): \WP_REST_Response
    {
        $response = [];
        return static::response_success($response);
    }

    public static function renderArgs(): array
    {
        return [
            'attributes' => [
                'type' => 'array',
                'default' => [],
                'validate_callback' => fn ($param) => is_array($param),
            ],
            'shortcode' => [
                'type' => 'string',
                'default' => '',
                'validate_callback' => fn ($param) => is_string($param),
            ],
        ];
    }

    public static function permission(): bool
    {
        return UserRole::can_current_user_use_visual_builder();
    }
}
