import {
    type ReactElement,
} from 'react';

import {
    type ModuleScriptDataProps,
} from '@divi/module';
import { type BlogAttrs } from '@divi/types';

/**
 * Blog module's script data component.
 *
 * @returns {ReactElement}
 */
export const ModuleScriptData = ({
    elements,
}: ModuleScriptDataProps<BlogAttrs>): ReactElement => elements.scriptData({
    attrName: 'module',
});
