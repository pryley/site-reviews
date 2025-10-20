import React, {
    type ReactElement,
} from 'react';

import {
    type ModuleScriptDataProps,
} from '@divi/module';

import { type ModuleAttrs } from './types';

export const ModuleScriptData = ({
    elements,
}: ModuleScriptDataProps<ModuleAttrs>): ReactElement => elements.scriptData({
    attrName: 'module',
});

