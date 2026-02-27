import React, { ReactElement } from 'react';
import { type ModuleAttrs } from './types';
import { type StylesProps, CssStyle, StyleContainer } from '@divi/module';
import {
    buttonAlignmentStyleDeclaration,
    colorStyleDeclaration,
    orientationStyleDeclaration,
} from '@site-reviews-divi/style-declarations';

const baseSelector = '.et-db #page-container .et_pb_section';

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
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.advanced?.text?.text,
                            declarationFunction: orientationStyleDeclaration,
                            selector: [
                                `${baseSelector} ${orderClass} .glsr-field:not(.glsr-layout-inline) .glsr-field-subgroup > *`,
                                `${baseSelector} ${orderClass} .glsr-layout-inline .glsr-field-subgroup`,
                                `${baseSelector} ${orderClass} .glsr-range-options input:checked + label`,
                                `${baseSelector} ${orderClass} .glsr-range-options:not(:has(input:checked))::after`,
                                `${baseSelector} ${orderClass} .glsr-star-rating`,
                            ].join(','),
                        },
                    },
                    {
                        componentName: "divi/text",
                        props: {
                            attr: attrs?.module?.advanced?.text,
                            propertySelectors: {
                                textShadow: {
                                    desktop: {
                                        value: {
                                            'text-shadow': [
                                                `${baseSelector} ${orderClass} .glsr-field`,
                                                `${baseSelector} ${orderClass} .glsr-input`,
                                                `${baseSelector} ${orderClass} .glsr-select`,
                                                `${baseSelector} ${orderClass} .glsr-textarea`,
                                            ].join(','),
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
                            declarationFunction: colorStyleDeclaration(['--glsr-form-star-bg']),
                            selector: `${orderClass}.has-custom-color .glsr-form`,
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
        <CssStyle
            selector={orderClass}
            attr={attrs?.css}
            orderClass={orderClass}
            cssFields={elements?.moduleMetadata?.customCssFields}
        />
    </StyleContainer>
);

export {
    ModuleStyles,
};
