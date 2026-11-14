import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _nx, _x, sprintf } from '@wordpress/i18n';
import { VisuallyHidden } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { ControlProps, Item, TransformedItem } from './types';
import { isSearchable } from '../utils';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useCallback, useEffect, useMemo, useRef, useState } from '@wordpress/element';
import SearchableChipSelectControl, { LoadingIndicator } from '../searchable-chip-select-control';
import type { Item as Choice } from '../searchable-results/types';

const toChoice = (item: TransformedItem): Choice => ({
    description: item.id,
    label: item.title,
    value: item.id,
});

const AjaxFormTokenField = (props: ControlProps) => {
    const {
        endpoint,
        help,
        hideLabelFromVision = false,
        label,
        onChange,
        placeholder,
        prefetch = false,
        storeName = DEFAULT_STORE_NAME,
        value,
    } = props;

    const [answered, setAnswered] = useState<string>(''); // the last search that finished
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [isSearching, setIsSearching] = useState<boolean>(false);
    const [search, setSearch] = useState<string>('');
    const [selectedValues, setSelectedValues] = useState<TransformedItem[]>([]);
    const hasFetchedData = useRef<boolean>(false);

    const registeredStoreName: string = createStore(storeName);
    const suggestedValues = useSelect<TransformedItem[]>(
        (select) => select(registeredStoreName).get('suggestedValues', endpoint),
        []
    );
    const { set: setStoreValues } = useDispatch(registeredStoreName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value.join(','),
            search,
        }),
    });

    const transformItem = (item: Item): TransformedItem => ({
        id: String(item.id),
        title: item.title,
        value: `${item.id}: ${item.title}`,
    });

    const sharedTitles = useMemo(() => {
        const idsByTitle = new Map<string, Set<string>>();
        [...suggestedValues, ...selectedValues].forEach((item) => {
            idsByTitle.set(item.title, (idsByTitle.get(item.title) || new Set()).add(item.id));
        });
        return new Set([...idsByTitle].filter(([, ids]) => ids.size > 1).map(([title]) => title));
    }, [selectedValues, suggestedValues]);

    const chipLabel = ({ label: title, value: id }: Choice): string => {
        return !isNaN(parseFloat(id)) || sharedTitles.has(title) ? `${id}: ${title}` : title;
    };

    const initValues = async () => {
        if (hasFetchedData.current) return;
        // Cached suggestions resolve the selection only when they hold every selected id
        const cachedSelection = suggestedValues.filter(
            (suggestion) => value.includes(suggestion.id)
        );
        const isResolvedFromCache = suggestedValues.length > 0 && cachedSelection.length === value.length;
        if (isResolvedFromCache || (!value.length && !prefetch)) {
            hasFetchedData.current = true; // Mark that we've fetched the data
            if (cachedSelection.length) {
                setSelectedValues(cachedSelection);
            }
            return;
        }
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>(req());
            hasFetchedData.current = true; // Mark that we've fetched the data
            const initialSuggestions: TransformedItem[] = [];
            const initialValues: TransformedItem[] = [];
            response.forEach((item) => {
                const transformed = transformItem(item);
                initialSuggestions.push(transformed);
                if (value.includes(transformed.id)) {
                    initialValues.push(transformed);
                }
            });
            setSelectedValues(initialValues);
            setStoreValues('suggestedValues', endpoint, initialSuggestions);
        } finally {
            setIsLoading(false);
        }
    };

    const performSearch = async () => {
        if (!isSearchable(search)) return;
        setIsSearching(true);
        try {
            const response = await apiFetch<Item[]>(req());
            setStoreValues('suggestedValues', endpoint, response.map(transformItem));
            setAnswered(search);
        } finally {
            setIsSearching(false);
        }
    };

    const handleValueChange = (choices: Choice[]) => {
        const known = new Map([...suggestedValues, ...selectedValues].map((item) => [item.id, item]));
        const nextValues = choices.map((choice) => known.get(choice.value)).filter(Boolean) as TransformedItem[];
        setSearch('');
        setSelectedValues(nextValues);
        onChange(nextValues.map((selected) => selected.id));
    };

    const performSearchRef = useRef(performSearch);
    performSearchRef.current = performSearch;
    const debouncedSearch = useDebounce(useCallback(() => performSearchRef.current(), []), 250);

    const choices = useMemo(() => {
        const text = search.toLocaleLowerCase();
        const isAnswered = !text || answered === search;
        return suggestedValues
            .filter((item) => isAnswered || item.value.toLocaleLowerCase().includes(text))
            .map(toChoice);
    }, [answered, search, suggestedValues]);

    const selectedChoices = useMemo(() => selectedValues.map(toChoice), [selectedValues]);

    // Run only on mount
    useEffect(() => { initValues() }, [])

    // Fetch suggestions whenever search changes
    useEffect(() => { debouncedSearch() }, [search])

    return (
        <SearchableChipSelectControl
            chipLabel={chipLabel}
            description={help}
            disabled={isLoading}
            emptyContent={isSearching ? null : undefined}
            hideLabelFromVision={hideLabelFromVision}
            items={choices}
            label={label || ''}
            onInputValueChange={setSearch}
            onValueChange={handleValueChange}
            searchPlaceholder={placeholder || _x('Search...', 'admin-text', 'site-reviews')}
            statusContent={isSearching ? <LoadingIndicator /> : (
                <VisuallyHidden>
                    {sprintf(
                        /* translators: %d: the number of items found */
                        _nx('%d result', '%d results', choices.length, 'admin-text', 'site-reviews'),
                        choices.length
                    )}
                </VisuallyHidden>
            )}
            value={selectedChoices}
        />
    );
};

export default AjaxFormTokenField;
