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
    defaultPrintedStyleAttrs,
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
                advancedStyles: [
                    {
                        componentName: "divi/text",
                        props: {
                            attr: attrs?.module?.advanced?.text,
                            propertySelectors: {
                                textShadow: {
                                    desktop: {
                                        value: {
                                            'text-shadow': orderClass,
                                        },
                                    },
                                },
                            }
                        },
                    },
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
                defaultPrintedStyleAttrs: defaultPrintedStyleAttrs?.module?.decoration,
                disabledOn: {
                    disabledModuleVisibility: settings?.disabledModuleVisibility,
                },
            },
        })}
    </StyleContainer>
)
};

export {
    ModuleStyles,
};
