import {
    textOptionsClassnames,
    type ModuleClassnamesParams,
} from '@divi/module';
import { isEmpty } from 'lodash';
import { type ModuleAttrs } from './types';

export const moduleClassnames = ({
    attrs,
    classnamesInstance,
}: ModuleClassnamesParams<ModuleAttrs>): void => {
    classnamesInstance.add(textOptionsClassnames(attrs?.module?.advanced?.text))
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
