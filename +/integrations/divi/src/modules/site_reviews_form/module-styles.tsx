import React, { ReactElement } from 'react';
import {
    StyleContainer,
    type StylesProps,
} from '@divi/module';
import {
    orientationStyleDeclaration,
} from './style-declarations';
import { type ModuleAttrs } from './types';

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
            attrName: 'button',
        })}
    </StyleContainer>
);

export {
    ModuleStyles,
};
