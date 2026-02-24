import React, { ReactElement } from 'react';
import {
    StyleContainer,
    TextStyle,
    type StylesProps,
} from '@divi/module';
import { colorStyleDeclaration } from '@site-reviews-divi/style-declarations';
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
                  selector: `${orderClass}.has-custom-color .glsr-review`,
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
