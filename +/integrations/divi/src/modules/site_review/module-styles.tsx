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
                advancedStyles: [
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleRatingColor,
                            property: '--glsr-review-star-bg',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleRatingSize,
                            property: '--glsr-review-star',
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
