<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class AssignedTermsField
{
    public const FIELD_COPY_ASSIGNED_TERMS = MetaboxFields::FIELD_COPY_TAXONOMIES;
    public const FIELD_COPY_ASSIGNED_TERMS_IS_CHECKED = 'multilingualpress.copy_assigned_terms_is_checked';

    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        glsr()->render('integrations/multilingualpress/fields/assigned_terms', [
            'id' => $helper->fieldId(static::FIELD_COPY_ASSIGNED_TERMS),
            'name' => $helper->fieldName(static::FIELD_COPY_ASSIGNED_TERMS),
            'checked' => glsr()->filterBool(static::FIELD_COPY_ASSIGNED_TERMS_IS_CHECKED, true),
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
