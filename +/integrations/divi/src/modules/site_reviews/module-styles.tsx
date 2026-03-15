import React, { ReactElement } from 'react';
import { type ModuleAttrs } from './types';
import { type StylesProps, CssStyle, StyleContainer, TextStyle } from '@divi/module';
import {
    buttonAlignmentStyleDeclaration,
    buttonIconStyleDeclaration,
    colorStyleDeclaration,
} from '@site-reviews-divi/style-declarations';

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
    const iconPlacementValue = attrs?.button?.decoration?.button?.desktop?.value?.icon?.placement ?? 'right';
    const iconPlacement = 'left' === iconPlacementValue ? 'before' : 'after';
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
                                selector: [
                                    `${baseSelector} ${orderClass}.has-custom-color .glsr-reviews`, // @todo remove in v8.1
                                    `${baseSelector} ${orderClass}.has-rating-color .glsr-reviews`,
                                ].join(','),
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
                                selector: `${baseSelector} ${orderClass} .glsr-button_wrapper`,
                            },
                        },
                        {
                            // Button Icon
                            componentName: "divi/common",
                            props: {
                                attr: attrs?.button?.decoration?.button,
                                declarationFunction: buttonIconStyleDeclaration,
                                selector: `${baseSelector} ${orderClass} .glsr-button::${iconPlacement}`,
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
}

export {
    ModuleStyles,
};
