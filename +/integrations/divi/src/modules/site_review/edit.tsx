import React, {
    type ReactElement,
    useEffect,
    useRef,
    useState,
} from 'react';
import {
    debounce,
    isArray,
    isNaN,
    join,
    toString,
} from 'lodash';
import objectHash from 'object-hash';

import { usePrevious } from '@wordpress/compose';

import { ModuleContainer } from '@divi/module';
import { getAttrByMode } from '@divi/module-utils';
import { useFetch } from '@divi/rest';
import { WindowEventEmitterInstance } from '@divi/script-library';
import { type BlogMetadata, type BlogPost } from '@divi/types';
import { Loading } from '@divi/ui-library';

import {
    LayoutFullwidth,
    LayoutGrid,
    NoResultsFound,
} from './components';
import { moduleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';
import { type BlogEditProps } from './types';

/**
 * Renders `Blog` edit component for visual builder.
 *
 * @param {BlogEditProps} props React component props.
 *
 * @returns {ReactElement}
 */
const ModuleEdit = (props: BlogEditProps): ReactElement => {
    /**
     * Setups initial variables.
     */
    const {
        attrs,
        id,
        isFirst,
        isLast,
        name,
        elements,
    } = props;

    const blogRef        = useRef(null);
    const postType       = getAttrByMode(attrs?.post?.advanced?.type);
    const categories     = getAttrByMode(attrs?.post?.advanced?.categories);
    const fullwidth      = getAttrByMode(attrs?.fullwidth?.advanced?.enable) || 'on';
    const dateFormat     = getAttrByMode(attrs?.post?.advanced?.dateFormat);
    const excerptContent = getAttrByMode(attrs?.post?.advanced?.excerptContent) || 'off';
    const manualExcerpt  = getAttrByMode(attrs?.post?.advanced?.excerptManual) || 'on';
    const showExcerpt    = getAttrByMode(attrs?.post?.advanced?.showExcerpt) || 'on';
    const showThumbnail  = 'on' === getAttrByMode(attrs?.image?.advanced?.enable);
    const showOverlay    = 'on' === getAttrByMode(attrs?.overlay?.advanced?.enable);
    const showReadMore   = 'on' === getAttrByMode(attrs?.readMore?.advanced?.enable) && 'on' !== excerptContent;
    const showPagination = 'on' === getAttrByMode(attrs?.pagination?.advanced?.enable);
    const showAuthor     = 'on' === getAttrByMode(attrs?.meta?.advanced?.showAuthor);
    const showDate       = 'on' === getAttrByMode(attrs?.meta?.advanced?.showDate);
    const showCategories = 'on' === getAttrByMode(attrs?.meta?.advanced?.showCategories);
    const showComments   = 'on' === getAttrByMode(attrs?.meta?.advanced?.showComments);
    const overlayIcon    = getAttrByMode(attrs?.overlayIcon?.decoration?.icon);
    const headingLevel   = getAttrByMode(attrs?.title?.decoration?.font?.font)?.headingLevel ?? 'h2';

    // Get the number of posts per page.
    let postsPerPage = parseInt(getAttrByMode(attrs?.post?.advanced?.number));

    if (isNaN(postsPerPage) || postsPerPage < 0) {
        postsPerPage = 10;
    }

    // Get the length of the excerpt.
    let excerptLength = parseInt(getAttrByMode(attrs?.post?.advanced?.excerptLength));

    if (isNaN(excerptLength) || excerptLength < 0) {
        excerptLength = 0;
    }

    // Get the offset.
    let offset = parseInt(getAttrByMode(attrs?.post?.advanced?.offset));

    if (isNaN(offset) || offset < 0) {
        offset = 0;
    }

    const [paged, setPaged] = useState(1);

    const setPagedNumber = (rawPaged:string | number) => {
        let parsed = parseInt(rawPaged.toString());

        if (isNaN(parsed) || parsed < 1) {
            parsed = 1;
        }

        setPaged(parsed);
    };

    const {
        fetch,
        response: { posts, metadata },
        isLoading,
        abort,
    } = useFetch<{posts: BlogPost[], metadata: BlogMetadata}>({ posts: [], metadata: {} });

    const categoriesStringified = isArray(categories) ? join(categories, ',') : toString(categories);

    // We need to keep track of the previous values of the `categoriesStringified`, `postsPerPage`, and `postType`
    // to determine whether we need to reset the `paged` to 1 or not.
    const categoriesStringifiedPrev = usePrevious(categoriesStringified);
    const postsPerPagePrev          = usePrevious(postsPerPage);
    const postTypePrev              = usePrevious(postType);

    const fetchDebounced = debounce(() => {
        let pagedNormalized = paged;

        // Reset the `paged` to 1 whenever `postType`, `postsPerPage`, or the `categories` is changed.
        if (postsPerPage !== postsPerPagePrev) {
            pagedNormalized = 1;
        }

        if (postType !== postTypePrev) {
            pagedNormalized = 1;
        }

        if (categoriesStringified !== categoriesStringifiedPrev) {
            pagedNormalized = 1;
        }

        if (pagedNormalized !== paged) {
            setPagedNumber(pagedNormalized);
        }

        fetch({
            method: 'GET',
            restRoute: '/glsr-divi/v1/module-data/blog/posts',
            data: {
                postType,
                postsPerPage,
                paged: pagedNormalized,
                categories: categoriesStringified,
                fullwidth,
                dateFormat,
                excerptContent,
                excerptLength,
                manualExcerpt,
                showExcerpt,
                offset,
            },
        }).then(() => {
            // Trigger an update on window's height, so components depending on it will re-render e.g. background video.
            // @ts-expect-error
            WindowEventEmitterInstance.trigger('height.changed');
        }).catch(error => {
            // TODO feat(D5, Logger) - We need to introduce a new logging system to log errors/rejections/etc.
            // eslint-disable-next-line no-console
            console.log(error);
        });
    }, 300);

    useEffect(() => {
        fetchDebounced();
        return () => {
            fetchDebounced.cancel();
            abort();
        };
    }, [
        postType,
        postsPerPage,
        paged,
        categoriesStringified,
        fullwidth,
        dateFormat,
        excerptContent,
        excerptLength,
        manualExcerpt,
        showExcerpt,
        offset,
    ]);

    const renderLoading = () => {
        if (! isLoading) {
            return null;
        }
        return (
            <Loading />
        );
    };

    const renderNoResultsFound = () => {
        const postsLength = posts?.length ?? 0;

        if (isLoading || postsLength) {
            return null;
        }
        return (
            <NoResultsFound />
        );
    };

    const renderPosts = () => {
        const postsLength = posts?.length ?? 0;

        if (isLoading || ! postsLength) {
            return null;
        }
        if ('on' === fullwidth) {
            return (
                <LayoutFullwidth
                    headingLevel={headingLevel}
                    overlayIcon={overlayIcon}
                    pagination={{
                        paged,
                        onChangePage: setPagedNumber,
                        metadata,
                    }}
                    moduleId={id}
                    posts={posts}
                    showOverlay={showOverlay}
                    showPagination={showPagination}
                    showReadMore={showReadMore}
                    showThumbnail={showThumbnail}
                    showAuthor={showAuthor}
                    showDate={showDate}
                    showCategories={showCategories}
                    showComments={showComments}
                />
            );
        }

        // Generate a key to force re-render for the LayoutGrid component when the posts data is changed.
        // This is necessary to avoid issues with the Salvattore script.
        const layoutGridRenderKey = `${id}--${objectHash(posts ?? {})}`;

        return (
            <LayoutGrid
                key={layoutGridRenderKey}
                headingLevel={headingLevel}
                overlayIcon={overlayIcon}
                pagination={{
                    paged,
                    onChangePage: setPagedNumber,
                    metadata,
                }}
                moduleId={id}
                posts={posts}
                showOverlay={showOverlay}
                showPagination={showPagination}
                showReadMore={showReadMore}
                showThumbnail={showThumbnail}
                showAuthor={showAuthor}
                showDate={showDate}
                showCategories={showCategories}
                showComments={showComments}
            />
        );
    };

    return (
        <ModuleContainer
            attrs={attrs}
            domRef={blogRef}
            elements={elements}
            id={id}
            isFirst={isFirst}
            isLast={isLast}
            stylesComponent={ModuleStyles}
            scriptDataComponent={ModuleScriptData}
            classnamesFunction={moduleClassnames}
            name={name}
        >
            {elements.styleComponents({
                attrName: 'module',
            })}
            {renderLoading()}
            {renderNoResultsFound()}
            {renderPosts()}
        </ModuleContainer>
    );
};

export {
    ModuleEdit,
};
