<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

use ET\Builder\Framework\Utility\ArrayUtility;
use ET\Builder\Packages\GlobalData\GlobalPresetItemGroupAttrNameResolved as AttrNameResolved;
use ET\Builder\Packages\GlobalData\GlobalPresetItemGroupAttrNameResolver as AttrNameResolver;

class PresetAttrsResolver
{
    /**
     * Resolve the group preset attribute name for the module.
     */
    public static function resolve(?AttrNameResolved $attr_name_to_resolve, array $params): ?AttrNameResolved
    {
        if (null !== $attr_name_to_resolve) {
            return $attr_name_to_resolve;
        }
        if ($params['moduleName'] === $params['dataModuleName']) {
            return $attr_name_to_resolve;
        }
        if ('glsr-divi/blog' !== $params['moduleName']) {
            return $attr_name_to_resolve;
        }
        if (false === strpos($params['attrName'], '.decoration.border')) {
            return $attr_name_to_resolve;
        }
        $attr_names_to_pairs = AttrNameResolver::get_attr_names_by_group($params['dataModuleName'], $params['dataGroupId']);
        $attr_name_match = ArrayUtility::find(
            $attr_names_to_pairs,
            fn ($attr_name) => AttrNameResolver::is_attr_name_suffix_matched($attr_name, $params['attrName'])
        );
        return new AttrNameResolved([
            'attrName' => $attr_name_match,
            'attrSubName' => $params['attrSubName'] ?? null,
        ]);
    }
}
