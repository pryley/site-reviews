import React, { ReactElement } from 'react';
import { type ModuleAttrs } from './types';
import { type StylesProps, CssStyle, StyleContainer, TextStyle } from '@divi/module';
import { colorStyleDeclaration } from '@site-reviews-divi/style-declarations';
import { isEmpty } from 'lodash';

const ModuleStyles = (props: StylesProps<ModuleAttrs>): ReactElement => {
const {
    attrs,
    defaultPrintedStyleAttrs,
    elements,
    mode,
    noStyleTag,
    orderClass,
    settings,
    state,
} = props;
const baseSelector = '#page-container';
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
                            selector: `${baseSelector} ${orderClass} .glsr`,
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
                            declarationFunction: colorStyleDeclaration(['--glsr-summary-star-bg']),
                            selector: `${baseSelector} ${orderClass}.has-custom-color .glsr-summary`,
                        },
                    },
                    {
                        // Bar Color
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.barColor,
                            declarationFunction: colorStyleDeclaration(['--glsr-bar-bg']),
                            selector: `${baseSelector} ${orderClass} .glsr-summary`,
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
)
};

export {
    ModuleStyles,
};
