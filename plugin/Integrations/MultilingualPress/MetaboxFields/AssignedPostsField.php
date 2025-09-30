<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class AssignedPostsField
{
    public const FIELD_COPY_ASSIGNED_POSTS = 'remote-assigned_posts-copy';
    public const FIELD_COPY_ASSIGNED_POSTS_IS_CHECKED = 'multilingualpress.copy_assigned_posts_is_checked';

    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        glsr()->render('integrations/multilingualpress/fields/assigned_posts', [
            'id' => $helper->fieldId(static::FIELD_COPY_ASSIGNED_POSTS),
            'name' => $helper->fieldName(static::FIELD_COPY_ASSIGNED_POSTS),
            'checked' => glsr()->filterBool(static::FIELD_COPY_ASSIGNED_POSTS_IS_CHECKED, true),
        ]);
    }

    /**
     * @param mixed $value
     */
    public static function sanitize($value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '';
    }
}
