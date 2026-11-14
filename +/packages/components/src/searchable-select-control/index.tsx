import { BaseControl, Icon } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { useEffect, useRef, useState } from '@wordpress/element';
import { _x } from '@wordpress/i18n';
import { chevronDown, closeSmall } from '@wordpress/icons';
import type { FocusEvent, KeyboardEvent } from 'react';
import SearchableResults, { LoadingIndicator, opensAbove } from '../searchable-results';
import type { Item } from '../searchable-results/types';
import type { ControlProps } from './types';
import '../searchable.scss';

/**
 * A clearable single select with a search field.
 *
 * @see @wordpress/ui SearchableSelectControl
 */
const SearchableSelectControl = (props: ControlProps) => {
    const {
        clearButtonLabel = _x('Clear', 'admin-text', 'site-reviews'),
        description,
        disabled = false,
        emptyContent,
        items,
        label,
        onInputValueChange,
        onValueChange,
        placeholder = _x('Select', 'admin-text', 'site-reviews'),
        searchPlaceholder = _x('Search', 'admin-text', 'site-reviews'),
        showClearButton = false,
        statusContent,
        value,
    } = props;

    const id = useInstanceId(SearchableSelectControl, 'glsr-searchable-select');
    // null highlights the first result or the selected item
    const [activeIndex, setActiveIndex] = useState<number | null>(null);
    const [inputValue, setInputValue] = useState<string>('');
    const [isAbove, setIsAbove] = useState<boolean>(false);
    const [isOpen, setIsOpen] = useState<boolean>(false);
    const field = useRef<HTMLDivElement>(null);
    const input = useRef<HTMLInputElement>(null);
    const trigger = useRef<HTMLButtonElement>(null);

    const isPicked = (item: Item): boolean => item.value === value?.value;

    const highlighted = null === activeIndex
        ? (inputValue && items.length ? 0 : items.findIndex(isPicked))
        : Math.min(activeIndex, items.length - 1);

    const search = (text: string) => {
        setActiveIndex(null);
        setInputValue(text);
        onInputValueChange?.(text);
    };

    const close = (isFocusKept: boolean = true) => {
        if (isFocusKept) {
            trigger.current?.focus();
        }
        if (isOpen) {
            search('');
            setIsOpen(false);
        }
    };

    const open = () => {
        if (field.current && !disabled) {
            setIsAbove(opensAbove(field.current));
            setIsOpen(true);
        }
    };

    const pick = (item: Item | null) => {
        close();
        onValueChange(item);
    };

    useEffect(() => {
        if (isOpen) {
            input.current?.focus();
        }
    }, [isOpen]);

    const handleBlur = (event: FocusEvent<HTMLDivElement>) => {
        if (!event.currentTarget.contains(event.relatedTarget)) {
            close(false);
        }
    };

    const handleInputKeyDown = (event: KeyboardEvent<HTMLInputElement>) => {
        const move = (step: number) => {
            if (items.length) {
                setActiveIndex(highlighted < 0 && step < 0 ? items.length - 1 : (highlighted + step + items.length) % items.length);
            }
            return true;
        };
        const keys: Record<string, () => boolean> = {
            ArrowDown: () => move(1),
            ArrowUp: () => move(-1),
            Enter: () => {
                if (items[highlighted]) {
                    pick(items[highlighted]);
                }
                return true;
            },
            Escape: () => {
                event.stopPropagation(); // don't close the popover
                close();
                return true;
            },
            Tab: () => {
                close();
                return true;
            },
        };
        if (keys[event.key]?.()) {
            event.preventDefault();
        }
    };

    const handleTriggerKeyDown = (event: KeyboardEvent<HTMLButtonElement>) => {
        if ([' ', 'ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) {
            event.preventDefault();
            open();
        }
    };

    return (
        <BaseControl
            __nextHasNoMarginBottom
            className="glsr-searchable-control"
            help={description}
            id={`${id}-trigger`}
            label={label}
        >
            <div className={isAbove ? 'glsr-searchable is-above' : 'glsr-searchable'} onBlur={handleBlur}>
                <div
                    className={disabled ? 'glsr-searchable__field is-disabled is-select' : 'glsr-searchable__field is-select'}
                    ref={field}
                >
                    <button
                        aria-controls={isOpen ? `${id}-popup` : undefined}
                        aria-describedby={description ? `${id}-trigger__help` : undefined}
                        aria-expanded={isOpen}
                        aria-haspopup="dialog"
                        className="glsr-searchable__trigger"
                        disabled={disabled}
                        id={`${id}-trigger`}
                        onClick={() => (isOpen ? close() : open())}
                        onKeyDown={handleTriggerKeyDown}
                        ref={trigger}
                        role="combobox"
                        type="button"
                    >
                        <span className={value ? 'glsr-searchable__trigger-text' : 'glsr-searchable__trigger-text is-placeholder'}>
                            {value ? value.label : placeholder}
                        </span>
                        <Icon icon={chevronDown} size={18} />
                    </button>
                    {showClearButton && !!value && (
                        <button
                            aria-label={clearButtonLabel}
                            className="glsr-searchable__clear"
                            disabled={disabled}
                            onClick={() => pick(null)}
                            onMouseDown={(event) => event.preventDefault()} // don't give focus
                            type="button"
                        >
                            <Icon icon={closeSmall} />
                        </button>
                    )}
                </div>
                {isOpen && (
                    <div aria-label={label} className="glsr-searchable__popup" id={`${id}-popup`} role="dialog">
                        <div className="glsr-searchable__search">
                            <input
                                aria-activedescendant={items[highlighted] ? `${id}-list-${highlighted}` : undefined}
                                aria-autocomplete="list"
                                aria-controls={`${id}-list`}
                                aria-expanded="true"
                                aria-label={searchPlaceholder}
                                autoComplete="off"
                                className="glsr-searchable__input"
                                onChange={(event) => search(event.target.value)}
                                onKeyDown={handleInputKeyDown}
                                placeholder={searchPlaceholder}
                                ref={input}
                                role="combobox"
                                type="text"
                                value={inputValue}
                            />
                        </div>
                        {/* keep focus in the search field */}
                        <div onMouseDown={(event) => event.preventDefault()}>
                            <SearchableResults
                                activeIndex={highlighted}
                                emptyContent={emptyContent}
                                id={`${id}-list`}
                                isPicked={isPicked}
                                items={items}
                                label={label}
                                onHover={setActiveIndex}
                                onPick={pick}
                                statusContent={statusContent}
                            />
                        </div>
                    </div>
                )}
            </div>
        </BaseControl>
    );
};

export { LoadingIndicator };

export default SearchableSelectControl;
