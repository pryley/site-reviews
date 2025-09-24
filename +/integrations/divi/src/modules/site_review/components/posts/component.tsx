import jQuery from 'jquery';
import React, {
    Fragment,
    type ReactElement,
    useEffect,
    useRef,
} from 'react';
import classNames from 'classnames';
import { includes, isEmpty } from 'lodash';

import { __ } from '@wordpress/i18n';

import {
    type BlogPost,
} from '@divi/types';

import { Heading } from '../heading/component';
import { PostMeta } from '../post-meta/component';
import { PostThumbnail } from '../post-thumbnail/component';
import { ReadMore } from '../read-more/component';
import { type PostsProps } from './types';


/**
 * Renders a list of blog posts.
 *
 * @param {PostsProps} props The component props.
 *
 * @returns {ReactElement} The rendered blog posts.
 */
const Posts = ({
    moduleId,
    posts,
    showOverlay,
    overlayIcon,
    headingLevel,
    showThumbnail,
    showThumbnailWithWrapper,
    showReadMore,
    showAuthor,
    showDate,
    showCategories,
    showComments,
    isFullwidth,
}: PostsProps): ReactElement => {
    const postVideoThumbRef = useRef();

    useEffect(() => {
        if (postVideoThumbRef?.current) {
            const $postVideoThumb = jQuery(postVideoThumbRef.current);
            // @ts-expect-error
            $postVideoThumb.fitVids();
        }
    });

    const renderThumbnail = (post: BlogPost) => {
        // @ts-expect-error
        if ('video' === post?.postFormat?.type && ! isEmpty(post?.postFormat?.video)) {
            return (
                <div className="et_main_video_container" ref={postVideoThumbRef}>
                    {
                        ! isEmpty(post?.thumbnail?.src) && (
                            <div
                                className="et_pb_video_overlay"
                                style={{
                                    backgroundImage: `url(${post?.thumbnail?.src})`,
                                    backgroundSize:  'cover',
                                }}
                            >
                                <div className="et_pb_video_overlay_hover">
                                    {/* eslint-disable */}
                                    <a href="#" className="et_pb_video_play" />
                                    {/* eslint-enable */}
                                </div>
                            </div>
                        )
                    }
                    <div
                        // @ts-expect-error
                        // eslint-disable-next-line react/no-danger
                        dangerouslySetInnerHTML={{ __html: post?.postFormat?.video }}
                    />
                </div>
            );
        }

        // @ts-expect-error
        if ('gallery' === post?.postFormat?.type && ! isEmpty(post?.postFormat?.gallery)) {
            return (
                <div
                    // @ts-expect-error
                    // eslint-disable-next-line react/no-danger
                    dangerouslySetInnerHTML={{ __html: post?.postFormat?.gallery }}
                />
            );
        }

        if (showThumbnail) {
            return (
                <PostThumbnail
                    post={post}
                    showOverlay={showOverlay}
                    overlayIcon={overlayIcon}
                    hasWrapper={showThumbnailWithWrapper}
                />
            );
        }

        return null;
    };

    const renderPosts = () => {
        if (! posts) {
            return null;
        }

        return posts.map((post, index) => {
            const showTitleMetaContent = ! isFullwidth
                // @ts-expect-error
                || ! includes(['link', 'audio', 'quote'], post?.postFormat?.type)
                // @ts-expect-error
                || post?.isPasswordRequired;


            return (
                <article
                    key={post.id}
                    className={classNames(post?.classNames, {
                        /* eslint-disable @typescript-eslint/naming-convention */
                        et_pb_post:                               true,
                        clearfix:                                 true,
                        et_pb_no_thumb:                           !! post?.thumbnail?.src && showThumbnail ? 'et_pb_no_thumb' : '',
                        et_pb_has_overlay:                        showOverlay,
                        [`et_pb_blog_item_${moduleId}_${index}`]: true,
                        /* eslint-enable @typescript-eslint/naming-convention */
                    })}
                >
                    {
                        // @ts-expect-error
                        'audio' === post?.postFormat?.type && (
                            <div
                                className={
                                    // @ts-expect-error
                                    classNames('et_audio_content', post?.postFormat?.textColorClass)
                                }
                                style={
                                    // @ts-expect-error
                                    { backgroundColor: post?.postFormat?.backgroundColor }
                                }
                            >
                                <h2><a href={post.permalink}>{post.title}</a></h2>
                                <div
                                    // @ts-expect-error
                                    // eslint-disable-next-line react/no-danger
                                    dangerouslySetInnerHTML={{ __html: post?.postFormat?.audio }}
                                />
                            </div>
                        )
                    }
                    {
                        // @ts-expect-error
                        'quote' === post?.postFormat?.type && (
                            <div
                                className={
                                    // @ts-expect-error
                                    classNames('et_quote_content', post?.postFormat?.textColorClass)
                                }
                                style={
                                    // @ts-expect-error
                                    { backgroundColor: post?.postFormat?.backgroundColor }
                                }
                            >
                                <div
                                    // @ts-expect-error
                                    // eslint-disable-next-line react/no-danger
                                    dangerouslySetInnerHTML={{ __html: post?.postFormat?.quote }}
                                />
                                <a className="et_quote_main_link" href={post?.permalink}>{__('Read more', 'et_builder')}</a>
                            </div>
                        )
                    }
                    {
                        // @ts-expect-error
                        'link' === post?.postFormat?.type && (
                            <div
                                className={
                                    // @ts-expect-error
                                    classNames('et_link_content', post?.postFormat?.textColorClass)
                                }
                                style={
                                    // @ts-expect-error
                                    { backgroundColor: post?.postFormat?.backgroundColor }
                                }
                            >
                                <h2><a href={post.permalink}>{post.title}</a></h2>
                                <a className="et_link_main_url" href={
                                    // @ts-expect-error
                                    post?.postFormat?.link
                                }>{
                                    // @ts-expect-error
                                    post?.postFormat?.link
                                }</a>
                            </div>
                        )
                    }
                            
                    {
                        // @ts-expect-error
                        (! includes(['link', 'audio', 'quote'], post?.postFormat?.type) || post?.isPasswordRequired) && renderThumbnail(post)}
                    {
                        showTitleMetaContent && (
                            // @ts-expect-error
                            ! includes(['link', 'audio'], post.postFormat?.type) || post?.isPasswordRequired
                        ) && (
                            <Heading headingLevel={headingLevel}>
                                <a href={post.permalink}>{post.title}</a>
                            </Heading>
                        )
                    }
                    {
                        showTitleMetaContent && (
                            <PostMeta
                                post={post}
                                showAuthor={showAuthor}
                                showDate={showDate}
                                showCategories={showCategories}
                                showComments={showComments}
                            />
                        )
                    }
                    {
                        showTitleMetaContent && (
                            <div className="post-content">
                                <div
                                    className="post-content-inner"
                                    // eslint-disable-next-line react/no-danger, @typescript-eslint/naming-convention
                                    dangerouslySetInnerHTML={{ __html: post.content }}
                                />
                                {showReadMore && <ReadMore permalink={post.permalink} />}
                            </div>
                        )
                    }
                </article>
            );
        });
    };

    return (
        <Fragment>
            {renderPosts()}
        </Fragment>
    );
};

export {
    Posts,
};
