import { type ModuleAttrs } from './types';
import { type ModuleClassnamesParams, textOptionsClassnames, getBackgroundLayoutClassnames } from '@divi/module';
import { isEmpty, isString } from 'lodash';

export const moduleClassnames = ({
    attrs,
    classnamesInstance,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text))
    classnamesInstance.add(getBackgroundLayoutClassnames(attrs?.module?.advanced?.text ?? {
        text: {
            desktop: {
                value: {
                    color: 'light',
                },
            },
        }
    }))
    // @ts-expect-error
    if (isEmpty(attrs?.shortcode?.advanced?.theme?.desktop?.value)) {
        // @ts-expect-error
        const ratingColor = attrs?.design?.decoration?.ratingColor?.desktop?.value?.color;
        classnamesInstance.add('has-rating-color', isString(ratingColor) && '' !== ratingColor)
    }
};
