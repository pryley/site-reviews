import React, {
    type MouseEvent,
    type ReactElement,
} from 'react';

import { __ } from '@wordpress/i18n';

import { type PaginationProps } from './types';

/**
 * Pagination component for the Blog module.
 *
 * @param {PaginationProps} props Pagination component props.
 *
 * @returns {ReactElement}
 */
const Pagination = ({ metadata, paged, onChangePage }: PaginationProps): ReactElement => {
    const maxNumPages   = metadata?.maxNumPages || 1;
    const wpPagenavi    = metadata?.wpPagenavi;
    const hasPrevPage   = paged < maxNumPages;
    const hasNextPage   = paged > 1 && paged <= maxNumPages;
    const hasWpPagenavi = !! wpPagenavi;

    const handleNextPage = (event?: MouseEvent<HTMLAnchorElement>) => {
        event.preventDefault();
        event.stopPropagation(); // Stop event propagation so the frontend script doesn't executed.

        onChangePage(paged - 1);
    };

    const handlePreviousPage = (event?: MouseEvent<HTMLAnchorElement>) => {
        event.preventDefault();
        event.stopPropagation(); // Stop event propagation so the frontend script doesn't executed.

        onChangePage(paged + 1);
    };

    /**
     * Navigates WP-PageNavi plugin pagination.
     *
     * @param {HTMLDivElement} event OnClick Event.
     *
     * @returns {void}
     */
    const handleWpPagenaviPagination = (event: MouseEvent<HTMLDivElement>) => {
        const target = event.target as unknown as Element;

        // Check if the target is an anchor element.
        if ('a' === target?.tagName?.toLowerCase()) {
            event.preventDefault();
            event.stopPropagation(); // Stop event propagation so the frontend script doesn't executed.

            if (target.classList.contains('last')) {
                onChangePage(maxNumPages);
            }

            if (target.classList.contains('first')) {
                onChangePage(1);
            }

            if (target.classList.contains('previouspostslink')) {
                onChangePage(paged - 1);
            }

            if (target.classList.contains('nextpostslink')) {
                onChangePage(paged + 1);
            }

            if (target.classList.contains('page')) {
                onChangePage(parseInt(target.textContent, 10));
            }
        }
    };

    if (hasWpPagenavi) {
        return (
            // eslint-disable-next-line max-len
            // eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/click-events-have-key-events
            <div
                role="navigation"
                aria-label="Pagination Navigation"
                onClick={handleWpPagenaviPagination}
                        // eslint-disable-next-line @typescript-eslint/naming-convention, react/no-danger
                dangerouslySetInnerHTML={{ __html: wpPagenavi }}
            />
        );
    }

    return (
        <div className="pagination clearfix">
            {hasPrevPage && (
            <div className="alignleft">
                <a
                    href="#prev"
                    aria-label={__('Previous Page', 'et_builder')}
                    onClick={handlePreviousPage}
                >
                    {__('« Older Entries', 'et_builder')}
                </a>
            </div>
            )}
            {hasNextPage && (
            <div className="alignright">
                <a
                    href="#next"
                    aria-label={__('Next Page', 'et_builder')}
                    onClick={handleNextPage}
                >
                    {__('Next Entries »', 'et_builder')}
                </a>
            </div>
            )}
        </div>
    );
};

export {
    Pagination,
};
