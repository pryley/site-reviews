import {
    textOptionsClassnames,
    type ModuleClassnamesParams,
} from '@divi/module';
import { isString } from 'lodash';
import { type ModuleAttrs } from './types';

export const moduleClassnames = ({
    attrs,
    classnamesInstance,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text))
    // @ts-expect-error
    const alignSelf = attrs?.module?.decoration?.sizing?.desktop?.value?.alignSelf;
    // @ts-expect-error
    classnamesInstance.add('items-justified-' + ({ start: 'left', end: 'right' }[alignSelf] ?? alignSelf), isString(alignSelf));
};
