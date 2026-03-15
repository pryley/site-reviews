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
        classnamesInstance.add('has-rating-color', isString(ratingColor) && '' !== ratingColor)
    }
    // @ts-expect-error
    const alignSelf = attrs?.module?.decoration?.sizing?.desktop?.value?.alignSelf;
    if (!isEmpty(alignSelf)) {
        const normalized = alignSelf.replace(/^flex-/, '').toLowerCase();
        const mapping: Record<string, string> = {
            'end': 'right',
            'start': 'left',
        };
        const justified = mapping[normalized] ?? normalized;
        classnamesInstance.add(`items-justified-${justified}`);
    }
};
