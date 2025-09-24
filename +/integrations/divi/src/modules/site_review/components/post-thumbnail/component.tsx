import React, { Fragment, type ReactElement } from 'react';

import { processFontIcon } from '@divi/icon-library';

import { type PostThumbnailProps } from './types';

/**
 * Post thumbnail component for the Blog module.
 *
 * @param {PostThumbnailProps} props Post thumbnail component props.
 *
 * @returns {ReactElement}
 */
const PostThumbnail = ({
    post,
    overlayIcon,
    hasWrapper,
    showOverlay,
}: PostThumbnailProps): ReactElement => {
    const iconValue = processFontIcon(overlayIcon);

    const renderOverlay = () => {
        if (! showOverlay) {
            return null;
        }

        return (
            <span
                className="et_overlay et_pb_inline_icon"
                data-icon={iconValue}
            />
        );
    };

    const renderThumbnail = () => {
        if (! post.thumbnail?.src) {
            return null;
        }

        return (
            <a href={post.permalink} className="entry-featured-image-url">
                <img
                    src={post?.thumbnail?.src}
                    className={
                        // @ts-expect-error
                        post?.thumbnail?.class
                    }
                    alt={post.thumbnail.alt}
                    srcSet={
                        // @ts-expect-error
                        post.thumbnail.srcset
                    }
                    sizes={
                        // @ts-expect-error
                        post.thumbnail.sizes
                    }
                    width={post.thumbnail.width}
                    height={post.thumbnail.height}
                />
                {renderOverlay()}
            </a>
        );
    };

    if (hasWrapper) {
        return (
            <div className="et_pb_image_container">
                {renderThumbnail()}
            </div>
        );
    }

    return (
        <Fragment>
            {renderThumbnail()}
        </Fragment>
    );
};

export {
    PostThumbnail,
};
