import React, { ReactElement } from 'react';
import { type ModuleAttrs } from './types';
import { type StylesProps, StyleContainer, TextStyle } from '@divi/module';
import {
    buttonAlignmentStyleDeclaration,
    colorStyleDeclaration,
} from '@site-reviews-divi/style-declarations';

const ModuleStyles = ({
    attrs,
    defaultPrintedStyleAttrs,
    elements,
    mode,
    noStyleTag,
    orderClass,
    settings,
    state,
}: StylesProps<ModuleAttrs>): ReactElement => (
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
                ],
                defaultPrintedStyleAttrs: defaultPrintedStyleAttrs?.module?.decoration,
                disabledOn: {
                    disabledModuleVisibility: settings?.disabledModuleVisibility,
                },
            },
        })}
        {elements.style({
            styleProps: {
                advancedStyles: [
                    {
                        // Rating Color
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.ratingColor,
                            declarationFunction: colorStyleDeclaration(['--glsr-review-star-bg']),
                            selector: `${orderClass}.has-custom-color .glsr-reviews`,
                        },
                    },
                ],
            },
        })}
        {elements.style({
            attrName: 'button',
            styleProps: {
                advancedStyles: [
                    {
                        // Button Alignment
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.button?.decoration?.button,
                            declarationFunction: buttonAlignmentStyleDeclaration,
                            selector: `${orderClass} .glsr-button_wrapper`,
                        },
                    },
                ],
            },
        })}
    </StyleContainer>
);

export {
    ModuleStyles,
};
