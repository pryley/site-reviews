<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class AssignedUsersField
{
    public const FIELD_COPY_ASSIGNED_USERS = 'remote-assigned_users-copy';
    public const FIELD_COPY_ASSIGNED_USERS_IS_CHECKED = 'multilingualpress.copy_assigned_users_is_checked';

    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        glsr()->render('integrations/multilingualpress/fields/assigned_users', [
            'id' => $helper->fieldId(static::FIELD_COPY_ASSIGNED_USERS),
            'name' => $helper->fieldName(static::FIELD_COPY_ASSIGNED_USERS),
            'checked' => glsr()->filterBool(static::FIELD_COPY_ASSIGNED_USERS_IS_CHECKED, true),
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
