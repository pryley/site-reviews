import React, {
    type ReactElement,
} from 'react';

import { Pagination } from '../pagination/component';
import { Posts } from '../posts/component';
import { type LayoutFullwidthProps } from './types';


/**
 * Renders a fullwidth layout for a blog component.
 *
 * @param {LayoutFullwidthProps} props The component props.
 *
 * @returns {ReactElement}
 */
const LayoutFullwidth = ({
    moduleId,
    posts,
    showOverlay,
    overlayIcon,
    headingLevel,
    showThumbnail,
    showReadMore,
    pagination,
    showPagination,
    showAuthor,
    showDate,
    showCategories,
    showComments,
}: LayoutFullwidthProps): ReactElement => {
    const renderPagination = () => {
        if (! showPagination) {
            return null;
        }

        return (
            <Pagination
                paged={pagination?.paged}
                onChangePage={pagination?.onChangePage}
                metadata={pagination?.metadata}
            />
        );
    };

    return (
        <div className="et_pb_ajax_pagination_container">
            <Posts
                headingLevel={headingLevel}
                overlayIcon={overlayIcon}
                moduleId={moduleId}
                posts={posts}
                showOverlay={showOverlay}
                showReadMore={showReadMore}
                showThumbnail={showThumbnail}
                showThumbnailWithWrapper={false}
                showAuthor={showAuthor}
                showDate={showDate}
                showCategories={showCategories}
                showComments={showComments}
                isFullwidth
            />
            {renderPagination()}
        </div>
    );
};

export {
    LayoutFullwidth,
};
