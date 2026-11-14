import { Icon, Spinner } from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';
import { _x } from '@wordpress/i18n';
import { check } from '@wordpress/icons';
import type { ReactNode } from 'react';
import type { ResultsProps } from './types';

const LIST_HEIGHT = 260; // the space needed below the field

export const opensAbove = (field: HTMLElement): boolean => {
    const { bottom, top } = field.getBoundingClientRect();
    const below = (field.ownerDocument.defaultView || window).innerHeight - bottom;
    return below < LIST_HEIGHT && top > below;
};

/**
 * The loading status of a list.
 */
export const LoadingIndicator = ({ children }: { children?: ReactNode }) => (
    <div className="glsr-searchable__loading">
        <Spinner />
        {children ?? _x('Loading…', 'admin-text', 'site-reviews')}
    </div>
);

/**
 * The status, empty state, and items of a searchable control's list.
 */
const SearchableResults = (props: ResultsProps) => {
    const { activeIndex, emptyContent, id, isPicked, items, label, multiple, onHover, onPick, statusContent } = props;
    const list = useRef<HTMLDivElement>(null);

    useEffect(() => {
        list.current?.children[activeIndex]?.scrollIntoView({ block: 'nearest' });
    }, [activeIndex]);

    return (
        <>
            <div aria-atomic="true" aria-live="polite" role="status">
                {statusContent}
            </div>
            {!items.length && null !== emptyContent && (
                <div className="glsr-searchable__empty">
                    {emptyContent ?? _x('No results found.', 'admin-text', 'site-reviews')}
                </div>
            )}
            <div
                aria-label={label}
                aria-multiselectable={multiple || undefined}
                className="glsr-searchable__list"
                id={id}
                ref={list}
                role="listbox"
            >
                {items.map((item, index) => (
                    <div
                        aria-selected={isPicked(item)}
                        className={index === activeIndex ? 'glsr-searchable__option is-active' : 'glsr-searchable__option'}
                        id={`${id}-${index}`}
                        key={item.value}
                        onClick={() => onPick(item)}
                        onMouseEnter={() => onHover(index)}
                        role="option"
                    >
                        <span className="glsr-searchable__option-indicator">
                            {isPicked(item) && <Icon icon={check} size={16} />}
                        </span>
                        <span className="glsr-searchable__option-label">{item.label}</span>
                        {!!item.description && (
                            <span className="glsr-searchable__option-description">{item.description}</span>
                        )}
                    </div>
                ))}
            </div>
        </>
    );
};

export default SearchableResults;
