import React, {
    type ReactElement,
    useEffect,
    useRef,
} from 'react';

import { salvattoreInit } from '../../script/salvattore-init';
import { Pagination } from '../pagination/component';
import { Posts } from '../posts/component';
import { type LayoutGridProps } from './types';

/**
 * Renders a grid layout for a blog component.
 *
 * @param {LayoutGridProps} props The component props.
 *
 * @returns {ReactElement}
 */
const LayoutGrid = ({
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
}: LayoutGridProps): ReactElement => {
    const salvattoreRef = useRef();
    const salvattoreDelayRef = useRef<ReturnType<typeof setTimeout>>();

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

    useEffect(() => {
        if (salvattoreDelayRef.current) {
            clearTimeout(salvattoreDelayRef.current);
        }

        salvattoreDelayRef.current = setTimeout(() => {
            if (salvattoreRef.current && posts && posts.length) {
                salvattoreInit(salvattoreRef.current);
            }
        }, 500);

        return () => {
            if (salvattoreDelayRef.current) {
                clearTimeout(salvattoreDelayRef.current);
            }
        };
    }, [JSON.stringify(posts)]);

    return (
        <div className="et_pb_blog_grid clearfix">
            <div
                className="et_pb_ajax_pagination_container"
            >
                <div
                    ref={salvattoreRef}
                    className="et_pb_salvattore_content"
                    data-columns
                >
                    <Posts
                        headingLevel={headingLevel}
                        overlayIcon={overlayIcon}
                        moduleId={moduleId}
                        posts={posts}
                        showOverlay={showOverlay}
                        showReadMore={showReadMore}
                        showThumbnail={showThumbnail}
                        showThumbnailWithWrapper
                        showAuthor={showAuthor}
                        showDate={showDate}
                        showCategories={showCategories}
                        showComments={showComments}
                    />
                </div>
                {renderPagination()}
            </div>
        </div>
    );
};

export {
    LayoutGrid,
};
