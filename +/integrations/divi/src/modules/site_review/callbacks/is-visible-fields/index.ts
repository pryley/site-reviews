import {
    getAttrValue,
} from '@divi/module-utils';
import {
    type BlogAttrs,
    type Module,
} from '@divi/types';

/**
 * Determines the visibility of setting fields based on specific parameters and conditions.
 *
 * @param {Module.Settings.Field.CallbackParams<BlogAttrs>} params Function parameters.
 *
 * @returns {boolean} Whether the field should be visible or not.
 */
export const isVisibleFields = (
    {
        attrs,
        // @ts-expect-error
        breakpoint,
        // @ts-expect-error
        baseBreakpoint,
        // @ts-expect-error
        breakpointNames,
        // @ts-expect-error
        state,
        attrName,
        subName,
    }: Module.Settings.Field.CallbackParams<BlogAttrs>,
): boolean => {
    const attrNameWithSubName = subName ? `${attrName}.*.${subName}` : attrName;

    switch (attrNameWithSubName) {
        case 'readMore.advanced.enable': // Content >> Elements >> Show Read More Button
        case 'post.advanced.showExcerpt': // Content >> Elements >> Show Excerpt
        case 'post.advanced.excerptManual': // Content >> Content >> Use Post Excerpt
        case 'post.advanced.excerptLength': // Content >> Content >> Excerpt Length
        {
            const excerptContent = getAttrValue({
                attr: attrs?.post?.advanced?.excerptContent,
                mode: 'getAndInheritAll',
                breakpoint,
                // @ts-expect-error
                baseBreakpoint,
                breakpointNames,
                state,
            });

            return 'on' !== excerptContent;
        }

        case 'post.advanced.categories': // Content >> Content >> Included Categories.
        {
            const postType = getAttrValue({
                attr: attrs?.post?.advanced?.type,
                mode: 'getAndInheritAll',
                breakpoint,
                // @ts-expect-error
                baseBreakpoint,
                breakpointNames,
                state,
            }) ?? 'post';

            if ('post' !== postType) {
                return false;
            }

            return true;
        }

        case 'masonry.decoration.background.*.color': // Content >> Background >> Grid Tile Background Color.
        {
            const fullwidth = getAttrValue({
                mode: 'getAndInheritAll',
                attr: attrs?.fullwidth?.advanced?.enable,
                breakpoint,
                // @ts-expect-error
                baseBreakpoint,
                breakpointNames,
                state,
            });

            return 'off' === fullwidth;
        }

        case 'overlay.decoration.background.*.color': // Design >> Overlay >> Overlay Background Color.
        case 'overlayIcon.decoration.icon.*.color': // Design >> Overlay >> Overlay Icon Color.
        case 'overlayIcon.decoration.icon': // Design >> Overlay >> Overlay Icon.
        {
            const overlayEnable = getAttrValue({
                mode: 'getAndInheritAll',
                attr: attrs?.overlay?.advanced?.enable,
                breakpoint,
                // @ts-expect-error
                baseBreakpoint,
                breakpointNames,
                state,
            });

            return 'on' === overlayEnable;
        }

        default: {
            return true;
        }
    }
};
