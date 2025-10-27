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
}: StylesProps<ModuleAttrs>): ReactElement => (
    <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
        {elements.style({
            attrName: 'module',
            styleProps: {
                disabledOn: {
                    disabledModuleVisibility: settings?.disabledModuleVisibility,
                },
            },
        })}
        {elements.style({
            attrName: 'design',
            styleProps: {
                advancedStyles: [
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.ratingColor,
                            property: '--glsr-review-star-bg',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.ratingSize,
                            property: '--glsr-review-star',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.reviewGap,
                            property: '--glsr-review-row-gap',
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
