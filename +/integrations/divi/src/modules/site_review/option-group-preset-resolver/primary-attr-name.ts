import {
    // @ts-expect-error
    type OptionGroupPresetPrimaryAttrNameResolverFilterParams,
} from '@divi/module-utils';


export const optionGroupPresetPrimaryAttrNameResolverBlog = (
    primaryAttrName: string,
    filterParams: OptionGroupPresetPrimaryAttrNameResolverFilterParams,
) => {
    // Set primaryAttrName for contentBackground composite group as it contains multiple attributes with similar suffixes.
    // - module.decoration.background.
    // - masonry.decoration.background.
    if ('contentBackground' === filterParams.groupId) {
        return 'module';
    }

    // Set primaryAttrName for designBorder composite group as it contains multiple attributes with similar suffixes.
    // - fullwidth.decoration.border.
    // - post.decoration.border.
    if ('designBorder' === filterParams.groupId) {
        return 'fullwidth';
    }

    return primaryAttrName;
};
