import React, { type ReactElement } from 'react';

import {
    CommonStyle,
    CssStyle,
    StyleContainer,
    type StylesProps,
    TextStyle,
} from '@divi/module';
import {
    type BlogAttrs,
} from '@divi/types';

import { cssFields } from './custom-css';
import { borderStyleDeclaration } from './style-declarations';


/**
 * Blog Module's style components.
 */
const ModuleStyles = ({
    attrs,
    elements,
    orderClass,
    settings,
    mode,
    state,
    noStyleTag,
}: StylesProps<BlogAttrs>): ReactElement => (
    <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
        {/* Module */}
        {elements.style({
            attrName:   'module',
            styleProps: {
                disabledOn: {
                    disabledModuleVisibility: settings?.disabledModuleVisibility,
                },
            },
        })}
        <TextStyle
            selector={orderClass}
            attr={attrs?.module?.advanced?.text}
            orderClass={orderClass}
        />

        {/* Image */}
        {elements.style({
            attrName: 'image',
        })}
        <CommonStyle
            selector={`${orderClass} .et_pb_post .entry-featured-image-url,${orderClass} .et_pb_post .et_pb_slides,${orderClass} .et_pb_post .et_pb_video_overlay`}
            attr={attrs?.image?.decoration?.border}
            declarationFunction={borderStyleDeclaration}
            orderClass={orderClass}
        />

        {/* Title */}
        {elements.style({
            attrName: 'title',
        })}
        {/* Meta */}
        {elements.style({
            attrName: 'meta',
        })}
        {/* Content */}
        {elements.style({
            attrName: 'content',
        })}
        {/* Read more */}
        {elements.style({
            attrName: 'readMore',
        })}
        {/* Post Item */}
        {elements.style({
            attrName: 'post',
        })}
        <CommonStyle
            selector={`${orderClass} .et_pb_post`}
            attr={attrs?.post?.decoration?.border}
            declarationFunction={borderStyleDeclaration}
            orderClass={orderClass}
        />

        {/* Fullwidth */}
        {elements.style({
            attrName: 'fullwidth',
        })}
        <CommonStyle
            selector={`${orderClass}:not(.et_pb_blog_grid_wrapper) .et_pb_post`}
            attr={attrs?.fullwidth?.decoration?.border}
            declarationFunction={borderStyleDeclaration}
            orderClass={orderClass}
        />
        {/* Overlay */}
        {elements.style({
            attrName: 'overlay',
        })}
        {/* Overlay Icon */}
        {elements.style({
            attrName: 'overlayIcon',
        })}

        {/* Masonry */}
        {elements.style({
            attrName: 'masonry',
        })}
        {/* Pagination */}
        {elements.style({
            attrName: 'pagination',
        })}

        <CssStyle
            selector={orderClass}
            attr={attrs.css}
            cssFields={cssFields}
            orderClass={orderClass}
        />
    </StyleContainer>
);

export {
    ModuleStyles,
};
