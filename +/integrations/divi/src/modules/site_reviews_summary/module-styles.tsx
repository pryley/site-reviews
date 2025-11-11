import React, { ReactElement } from 'react';
import {
    StyleContainer,
    TextStyle,
    type StylesProps,
} from '@divi/module';
import { isEmpty } from 'lodash';
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
                advancedStyles: [
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.sizing,
                            // @ts-expect-error
                            declarationFunction: ({attrValue: { maxWidth = '' }}) => {
                                return !isEmpty(maxWidth) ? `--glsr-max-w:none;` : '';
                            },
                            selector: `${orderClass} .glsr`,
                        },
                    },
                ],
            },
        })}
    </StyleContainer>
)
};

export {
    ModuleStyles,
};
