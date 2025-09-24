import {
    getAttrValue,
} from '@divi/module-utils';
import {
    type BlogAttrs,
    type Module,
} from '@divi/types';


/**
 * Determines the visibility of option groups based on specific parameters and conditions.
 *
 * @param {Module.Settings.Group.VisibleCallback.Params<BlogAttrs>} params Function parameters.
 *
 * @returns {boolean} Whether the group should be visible or not.
 */
export const isVisibleGroup = (
    {
        attrs,
        breakpoint,
        baseBreakpoint,
        breakpointNames,
        state,
        groupId,
    // @ts-expect-error
    }: Module.Settings.Group.VisibleCallback.Params<BlogAttrs>,
): boolean => {
    const fullwidth = getAttrValue({
        mode: 'getAndInheritAll',
        attr: attrs?.fullwidth?.advanced?.enable,
        breakpoint,
        // @ts-expect-error
        baseBreakpoint,
        breakpointNames,
        state,
    });

    switch (groupId) {
        case 'post.decoration.border': // Design >> Border >> Grid Layout.
        {
            return 'off' === fullwidth;
        }

        case 'fullwidth.decoration.border': // Design >> Border >> Fullwidth Layout.
        {
            return 'off' !== fullwidth;
        }

        default: {
            return true;
        }
    }
};
