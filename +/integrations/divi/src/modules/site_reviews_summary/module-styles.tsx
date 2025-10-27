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
        {elements.style({
            attrName: 'design',
            styleProps: {
                advancedStyles: [
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.ratingColor,
                            declarationFunction: ({ attrValue }) => {
                                return `--glsr-bar-bg:${attrValue}; --glsr-summary-star-bg:var(--glsr-bar-bg);`;
                            },
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.ratingSize,
                            property: '--glsr-summary-star',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.barSize,
                            property: '--glsr-bar-size',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.design?.decoration?.barSpacing,
                            property: '--glsr-bar-spacing',
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
