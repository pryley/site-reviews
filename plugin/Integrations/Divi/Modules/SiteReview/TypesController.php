<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

use ET\Builder\Framework\Controllers\RESTController;
use ET\Builder\Framework\UserRole\UserRole;

class TypesController extends RESTController
{
    public static function index(\WP_REST_Request $request): \WP_REST_Response
    {
        $post_types = et_get_registered_post_type_options(false, false);
        $response = [];
        if (!empty($post_types)) {
            foreach ($post_types as $post_type => $post_type_label) {
                // Exclude Media/attachment post type from the Blog module post type selection.
                if ('attachment' === $post_type) {
                    continue;
                }
                $response[$post_type] = [
                    'label' => $post_type_label,
                ];
            }
        }
        return static::response_success($response);
    }

    public static function index_args(): array
    {
        return [];
    }

    public static function index_permission(): bool
    {
        return UserRole::can_current_user_use_visual_builder();
    }
}
