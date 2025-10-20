import {
    elementClassnames,
    textOptionsClassnames,
    type ModuleClassnamesParams,
} from '@divi/module';

import { type ModuleAttrs } from './types';

export const moduleClassnames = ({
    attrs,
    breakpoint,
    classnamesInstance,
    state,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    classnamesInstance.add(
        textOptionsClassnames(attrs?.module?.advanced?.text)
    );
    classnamesInstance.add(
        elementClassnames({
            attrs: {
                ...attrs?.module?.decoration ?? {},
            },
            breakpoint,
            state,
        }),
    );
};
