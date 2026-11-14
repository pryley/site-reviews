import { BaseControl, Icon, VisuallyHidden } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { useRef, useState } from '@wordpress/element';
import { _nx, _x, isRTL, sprintf } from '@wordpress/i18n';
import { closeSmall } from '@wordpress/icons';
import type { FocusEvent, KeyboardEvent, MouseEvent } from 'react';
import SearchableResults, { LoadingIndicator, opensAbove } from '../searchable-results';
import type { Item } from '../searchable-results/types';
import type { ControlProps } from './types';
import '../searchable.scss';

/**
 * A searchable multi-select with its picked items as chips.
 *
 * @see @wordpress/ui SearchableChipSelectControl
 */
const SearchableChipSelectControl = (props: ControlProps) => {
    const {
        chipLabel = (item: Item) => item.label,
        description,
        disabled = false,
        emptyContent,
        hideLabelFromVision = false,
        items,
        label,
        onInputValueChange,
        onValueChange,
        searchPlaceholder = _x('Search', 'admin-text', 'site-reviews'),
        statusContent,
        value,
    } = props;

    const id = useInstanceId(SearchableChipSelectControl, 'glsr-searchable-chip-select');
    // null highlights the first result while typing
    const [activeIndex, setActiveIndex] = useState<number | null>(null);
    const [inputValue, setInputValue] = useState<string>('');
    const [isAbove, setIsAbove] = useState<boolean>(false);
    const [isOpen, setIsOpen] = useState<boolean>(false);
    const chips = useRef<(HTMLDivElement | null)[]>([]);
    const field = useRef<HTMLDivElement>(null);
    const input = useRef<HTMLInputElement>(null);
    const [back, forward] = isRTL() ? ['ArrowRight', 'ArrowLeft'] : ['ArrowLeft', 'ArrowRight'];

    const isPicked = (item: Item): boolean => value.some((picked) => picked.value === item.value);

    const highlighted = null === activeIndex
        ? (inputValue && items.length ? 0 : -1)
        : Math.min(activeIndex, items.length - 1);

    const close = () => {
        setActiveIndex(null);
        setIsOpen(false);
    };

    const open = () => {
        if (field.current && !isOpen) {
            setIsAbove(opensAbove(field.current));
            setIsOpen(true);
        }
    };

    const search = (text: string) => {
        setActiveIndex(null);
        setInputValue(text);
        onInputValueChange?.(text);
    };

    const toggle = (item: Item) => {
        onValueChange(isPicked(item) ? value.filter((picked) => picked.value !== item.value) : [...value, item]);
        search('');
    };

    const remove = (index: number, focus: HTMLElement | null | undefined) => {
        focus?.focus();
        onValueChange(value.filter((picked, position) => position !== index));
    };

    const handleBlur = (event: FocusEvent<HTMLDivElement>) => {
        if (!event.currentTarget.contains(event.relatedTarget)) {
            close();
        }
    };

    const handleChipKeyDown = (event: KeyboardEvent<HTMLDivElement>, index: number) => {
        const toSearch = () => input.current?.focus();
        const openList = () => {
            open();
            toSearch();
        };
        const removeChip = () => remove(index, chips.current[index + 1] ?? chips.current[index - 1] ?? input.current);
        const keys: Record<string, () => void> = {
            ' ': toSearch,
            ArrowDown: openList,
            [back]: () => (chips.current[index - 1] ?? input.current)?.focus(),
            [forward]: () => (chips.current[index + 1] ?? input.current)?.focus(),
            ArrowUp: openList,
            Backspace: removeChip,
            Delete: removeChip,
            Enter: toSearch,
        };
        if (keys[event.key]) {
            event.preventDefault();
            keys[event.key]();
        } else if (1 === event.key.length && !event.altKey && !event.ctrlKey && !event.metaKey) {
            toSearch();
        }
    };

    const handleFieldMouseDown = (event: MouseEvent<HTMLDivElement>) => {
        if (disabled || (event.target as HTMLElement).closest('button')) {
            return;
        }
        if (event.target !== input.current) {
            event.preventDefault();
            input.current?.focus();
        }
        open();
    };

    const handleInputKeyDown = (event: KeyboardEvent<HTMLInputElement>) => {
        const move = (step: number) => {
            if (!isOpen) {
                open();
            } else if (items.length) {
                setActiveIndex(highlighted < 0 && step < 0 ? items.length - 1 : (highlighted + step + items.length) % items.length);
            }
            return true;
        };
        const keys: Record<string, () => boolean> = {
            ArrowDown: () => move(1),
            [back]: () => {
                const isAtStart = 0 === (event.currentTarget.selectionStart ?? 0);
                if (isAtStart && value.length) {
                    close();
                    chips.current[value.length - 1]?.focus();
                }
                return isAtStart && !!value.length;
            },
            ArrowUp: () => move(-1),
            Backspace: () => {
                const isLastPicked = !inputValue && !!value.length;
                if (isLastPicked) {
                    remove(value.length - 1, input.current);
                }
                return isLastPicked;
            },
            Enter: () => {
                const item = isOpen ? items[highlighted] : undefined;
                if (item) {
                    toggle(item);
                }
                return !!item;
            },
            Escape: () => {
                if (isOpen) {
                    event.stopPropagation(); // don't close the popover
                    close();
                }
                return isOpen;
            },
        };
        if (keys[event.key]?.()) {
            event.preventDefault();
        }
    };

    const hint = value.length
        ? sprintf(
            /* translators: 1: the number of selected items, 2: the name of an arrow key */
            _nx(
                '%1$d item selected. From the start of the input, press %2$s to move to the selected item.',
                '%1$d items selected. From the start of the input, press %2$s to move to the selected items.',
                value.length,
                'admin-text',
                'site-reviews'
            ),
            value.length,
            isRTL() ? _x('Right Arrow', 'admin-text', 'site-reviews') : _x('Left Arrow', 'admin-text', 'site-reviews')
        )
        : '';
    const describedBy = [description ? `${id}-input__help` : '', hint ? `${id}-selected` : ''].filter(Boolean).join(' ');

    return (
        <BaseControl
            __nextHasNoMarginBottom
            className="glsr-searchable-control"
            help={description}
            hideLabelFromVision={hideLabelFromVision}
            id={`${id}-input`}
            label={label}
        >
            <div className={isAbove ? 'glsr-searchable is-above' : 'glsr-searchable'} onBlur={handleBlur}>
                <div
                    aria-label={value.length
                        ? _nx('Selected item', 'Selected items', value.length, 'admin-text', 'site-reviews')
                        : undefined}
                    className={disabled ? 'glsr-searchable__field is-disabled' : 'glsr-searchable__field'}
                    onMouseDown={handleFieldMouseDown}
                    ref={field}
                    role={value.length ? 'toolbar' : undefined}
                >
                    {!!value.length && (
                        <div className="glsr-searchable__chips">
                            {value.map((item, index) => (
                                <div
                                    aria-describedby={`${id}-remove`}
                                    aria-labelledby={`${id}-chip-${index}`}
                                    className="glsr-searchable__chip"
                                    key={item.value}
                                    onKeyDown={(event) => handleChipKeyDown(event, index)}
                                    ref={(element) => {
                                        chips.current[index] = element;
                                    }}
                                    tabIndex={-1} // focused by the arrow keys
                                >
                                    <span className="glsr-searchable__chip-label" id={`${id}-chip-${index}`}>
                                        {chipLabel(item)}
                                    </span>
                                    <button
                                        aria-describedby={`${id}-chip-${index}`}
                                        aria-label={_x('Remove', 'admin-text', 'site-reviews')}
                                        className="glsr-searchable__chip-remove"
                                        disabled={disabled}
                                        onClick={() => remove(index, input.current)}
                                        onMouseDown={(event) => event.preventDefault()} // don't give focus
                                        tabIndex={-1}
                                        type="button"
                                    >
                                        <Icon icon={closeSmall} />
                                    </button>
                                </div>
                            ))}
                        </div>
                    )}
                    <input
                        aria-activedescendant={isOpen && items[highlighted] ? `${id}-list-${highlighted}` : undefined}
                        aria-autocomplete="list"
                        aria-controls={`${id}-list`}
                        aria-describedby={describedBy || undefined}
                        aria-expanded={isOpen}
                        autoComplete="off"
                        className="glsr-searchable__input"
                        disabled={disabled}
                        id={`${id}-input`}
                        onChange={(event) => {
                            search(event.target.value);
                            open();
                        }}
                        onKeyDown={handleInputKeyDown}
                        placeholder={searchPlaceholder}
                        ref={input}
                        role="combobox"
                        type="text"
                        value={inputValue}
                    />
                </div>
                {isOpen && (
                    // keep focus in the search field
                    <div className="glsr-searchable__popup" onMouseDown={(event) => event.preventDefault()}>
                        <SearchableResults
                            activeIndex={highlighted}
                            emptyContent={emptyContent}
                            id={`${id}-list`}
                            isPicked={isPicked}
                            items={items}
                            label={label}
                            multiple
                            onHover={setActiveIndex}
                            onPick={toggle}
                            statusContent={statusContent}
                        />
                    </div>
                )}
                <VisuallyHidden aria-hidden="true" id={`${id}-remove`}>
                    {_x('Press Backspace or Delete to remove.', 'admin-text', 'site-reviews')}
                </VisuallyHidden>
                {!!hint && (
                    <VisuallyHidden aria-hidden="true" id={`${id}-selected`}>
                        {hint}
                    </VisuallyHidden>
                )}
            </div>
        </BaseControl>
    );
};

export { LoadingIndicator };

export default SearchableChipSelectControl;
