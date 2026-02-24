import { type ModuleAttrs } from './types';
import { type ModuleClassnamesParams, textOptionsClassnames } from '@divi/module';
import { isEmpty, isString } from 'lodash';

export const moduleClassnames = ({
    attrs,
    classnamesInstance,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text))
    // @ts-expect-error
    if (isEmpty(attrs?.shortcode?.advanced?.theme?.desktop?.value)) {
        // @ts-expect-error
        const ratingColor = attrs?.design?.decoration?.ratingColor?.desktop?.value?.color;
        classnamesInstance.add('has-custom-color', isString(ratingColor) && '' !== ratingColor)
    }
};
