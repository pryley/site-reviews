<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Metabox;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class AssignedUsersField
{
    public const FIELD_COPY_ASSIGNED_USERS_IS_CHECKED = 'copy_assigned_users_is_checked';

    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        glsr()->render('integrations/multilingualpress/assigned_users-field', [
            'id' => $helper->fieldId(MetaboxFields::FIELD_COPY_ASSIGNED_USERS),
            'name' => $helper->fieldName(MetaboxFields::FIELD_COPY_ASSIGNED_USERS),
            'checked' => glsr()->filterBool(static::FIELD_COPY_ASSIGNED_USERS_IS_CHECKED, true),
        ]);
    }

    public static function sanitize($value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '';
    }
}
