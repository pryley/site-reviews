<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class FieldCopier
{
    protected Copier $copier;

    public function __construct(Copier $copier)
    {
        $this->copier = $copier;
    }

    /**
     * Handle the copy of Meta Fields
     *
     * The Method is a callback for PostRelationSaveHelper::FILTER_SYNC_KEYS filter
     * It will receive the keys of the meta fields which should be synced and
     * will add the meta field keys
     *
     * @param array $keysToSync The list of meta keys where should be added the meta field keys to be synced
     * @param RelationshipContext $context
     * @param Request $request
     * @return array The list of meta keys to be synced
     * @throws NonexistentTable
     */
    public function handleCopyACFFields(
        array $keysToSync,
        RelationshipContext $context,
        Request $request
    ): array {
        $multilingualpress = $request->bodyValue('multilingualpress', INPUT_POST, FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        $remoteSiteId = $context->remoteSiteId();
        $translation = $multilingualpress["site-{$remoteSiteId}"] ?? '';
        if (empty($translation) || !get_field_objects()) {
            return $keysToSync;
        }
        $fieldObjects = $this->getFieldObjects(get_the_ID());

        $metaKeys = $this->extractACFFieldMetaKeys($fieldObjects);
        $this->handleSpecialACFFieldTypes($fieldObjects, $context);
        return array_merge($keysToSync, $metaKeys);
    }

    /**
     * Gets the ACF field objects.
     *
     * Gets the ACF field object based on post meta key
     *
     * @param int $postId The id for the post for which to get ACF field objects
     * @return array The list of advanced custom fields
     * @psalm-return array<Field> The list of advanced custom fields
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    private function getFieldObjects(int $postId): array
    {
        $fieldObjects = [];
        $meta = get_post_meta($postId);

        foreach ($meta as $metaKey => $metaValue) {
            $fieldObject = get_field_object($metaKey);

            // get_field_object returns false if the meta key is not an ACF field
            if (empty($fieldObject)) {
                $fieldObject = $this->handleCloneFields($metaValue[0] ?? '', $metaKey);
                if (empty($fieldObject)) {
                    continue;
                }
            }

            $fieldObjects[] = $fieldObject;
        }

        return $fieldObjects;
    }
}
