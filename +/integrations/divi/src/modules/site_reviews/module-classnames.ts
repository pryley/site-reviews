import {
    elementClassnames,
    textOptionsClassnames,
    type ModuleClassnamesParams,
} from '@divi/module';
import { isString } from 'lodash';
import { type ModuleAttrs } from './types';

export const moduleClassnames = ({
    attrs,
    breakpoint,
    classnamesInstance,
    state,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    // @ts-expect-error
    const alignSelf = attrs?.module?.decoration?.sizing?.desktop?.value?.alignSelf;
    const ratingColor = attrs?.review?.decoration?.ratingColor?.desktop?.value;
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text))
    classnamesInstance.add('has-custom-color', isString(ratingColor) && '' !== ratingColor)
    // @ts-expect-error
    classnamesInstance.add('items-justified-' + ({ start: 'left', end: 'right' }[alignSelf] ?? alignSelf), isString(alignSelf));
};
