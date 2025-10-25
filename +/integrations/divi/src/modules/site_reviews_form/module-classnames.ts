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
    const ratingColor = attrs?.form?.decoration?.ratingColor?.desktop?.value;
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text));
    classnamesInstance.add('has-custom-color', isString(ratingColor) && '' !== ratingColor)
};
