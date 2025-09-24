import React, { type ReactElement } from 'react';
import { map } from 'lodash';

import { __, sprintf } from '@wordpress/i18n';

import { type PostMetaProps } from './types';

/**
 * Post meta component for the Blog module.
 *
 * @param {PostMetaProps} props Post meta component props.
 *
 * @returns {ReactElement}
 */
const PostMeta = ({
    post,
    showAuthor,
    showDate,
    showCategories,
    showComments,
}: PostMetaProps): ReactElement => {
    const postMeta: string[] = [];

    if (showAuthor) {
        postMeta.push(sprintf(__('by %s'), `<span class="author vcard"><a href="${post?.author?.link}" title="${sprintf(__('Posts by %s', 'et_builder'), post?.author?.name)}" rel="author">${post?.author?.name}</a></span>`));
    }

    if (showDate) {
        postMeta.push(`<span class="published">${post.date}</span>`);
    }

    if (showCategories) {
        postMeta.push(map(post.categories, category => (`<a href="${category.link}" rel="tag">${category.name}</a>`)).join(', '));
    }

    if (showComments) {
        postMeta.push(post.comment);
    }

    return (
        // eslint-disable-next-line react/no-danger, @typescript-eslint/naming-convention
        <p className="post-meta" dangerouslySetInnerHTML={{ __html: postMeta.join(' | ') }} />
    );
};

export {
    PostMeta,
};
