import {
    elementClassnames,
    textOptionsClassnames,
    type ModuleClassnamesParams,
} from '@divi/module';

import { type BlogAttrs } from '@divi/types';

/**
 * Module classnames function for Audio module.
 *
 * @param {ModuleClassnamesParams<BlogAttrs>} param0 Function parameters.
 */
export const moduleClassnames = ({
    classnamesInstance,
    attrs,
    state,
    breakpoint,
}: ModuleClassnamesParams<BlogAttrs>): void => {
    // Fullwidth and grid class
    const fullwidth = attrs?.fullwidth?.advanced?.enable?.desktop?.value;
    if ('on' === fullwidth) {
        classnamesInstance.add('et_pb_posts');
    } else {
        classnamesInstance.add('et_pb_blog_grid_wrapper');
    }

    // Text Options.
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text));

    // Add element classnames.
    classnamesInstance.add(
        elementClassnames({
            attrs: {
                ...attrs?.module?.decoration ?? {},
                border: attrs?.post?.decoration?.border ?? attrs?.fullwidth?.decoration?.border ?? {},
            },
            state,
            breakpoint,
        }),
    );
};
