import React, { ReactElement } from 'react';
import {
    StyleContainer,
    TextStyle,
    type StylesProps,
} from '@divi/module';

import { type ModuleAttrs } from './types';

 const ModuleStyles = ({
    attrs,
    elements,
    mode,
    noStyleTag,
    orderClass,
    settings,
    state,
}: StylesProps<ModuleAttrs>): ReactElement => {
    return (
        <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
            {elements.style({
                attrName: 'module',
                styleProps: {
                    disabledOn: {
                        disabledModuleVisibility: settings?.disabledModuleVisibility,
                    },
                    advancedStyles: [],
                },
            })}
        </StyleContainer>
    );
}

export {
    ModuleStyles,
};
