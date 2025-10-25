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
                            declarationFunction: ({attrValue: {maxWidth = 'none'}}) => `--glsr-max-w:${maxWidth};`,
                            selector: `${orderClass} .glsr`,
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleRatingColor,
                            declarationFunction: ({ attrValue }) => {
                                return `--glsr-bar-bg:${attrValue};--glsr-summary-star-bg:var(--glsr-bar-bg);`;
                            },
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleBarSize,
                            property: '--glsr-bar-size',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleBarSpacing,
                            property: '--glsr-bar-spacing',
                        },
                    },
                    {
                        componentName: "divi/common",
                        props: {
                            attr: attrs?.module?.decoration?.styleRatingSize,
                            property: '--glsr-summary-star',
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
