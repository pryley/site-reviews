import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _nx, _x, sprintf } from '@wordpress/i18n';
import { VisuallyHidden } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { ControlProps, Item, TransformedItem } from './types';
import { isSearchable } from '../utils';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useMemo, useRef, useState } from '@wordpress/element';
import SearchableSelectControl, { LoadingIndicator } from '../searchable-select-control';
import type { Item as Choice } from '../searchable-results/types';

const AjaxSearchControl = (props: ControlProps) => {
    const {
        endpoint,
        help,
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
    const [typed, setTyped] = useState<string>('');
    const hasFetchedData = useRef<boolean>(false);
    const isFirstRun = useRef<boolean>(true);

    const registeredStoreName = createStore(storeName);
    const options = useSelect<TransformedItem[]>(
        (select) => select(registeredStoreName).get('options', endpoint),
        []
    );
    const { set: setStoreValues } = useDispatch(registeredStoreName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value,
            search,
        }),
    });

    const debouncedSearch = useDebounce(setSearch, 250);

    const transformItem = (item: Item): TransformedItem => ({
        label: !isNaN(parseFloat(item.id)) ? `${item.id}: ${item.title}` : item.title,
        title: item.title,
        value: String(item.id),
    });

    const initOptions = async () => {
        if (hasFetchedData.current) return;
        if (options.length || (!value && !prefetch)) {
            hasFetchedData.current = true; // Mark that we've fetched the data
            return;
        }
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>(req());
            hasFetchedData.current = true; // Mark that we've fetched the data
            setStoreValues('options', endpoint, response.map(transformItem));
        } finally {
            setIsLoading(false);
        }
    };

    const performSearch = async () => {
        if (!isSearchable(search) || isFirstRun.current) {
            isFirstRun.current = false;
            return;
        }
        setIsSearching(true);
        try {
            const response = await apiFetch<Item[]>(req());
            setStoreValues('options', endpoint, response.map(transformItem));
            setAnswered(search);
        } finally {
            setIsSearching(false);
        }
    };

    const choices = useMemo(() => {
        const text = typed.toLocaleLowerCase();
        const isAnswered = !text || answered === typed;
        return options
            .filter((option) => isAnswered || `${option.value}: ${option.title}`.toLocaleLowerCase().includes(text))
            .map((option): Choice => ({ description: option.value, label: option.title, value: option.value }));
    }, [answered, options, typed]);

    const selected = useMemo(() => {
        const option = options.find((candidate) => candidate.value === String(value ?? ''));
        return option ? { label: option.label, value: option.value } : null;
    }, [options, value]);

    const isWaiting = isLoading || isSearching;

    // Run only on mount
    useEffect(() => { initOptions() }, [])

    // Fetch options whenever search changes
    useEffect(() => { performSearch() }, [search])

    return (
        <SearchableSelectControl
            description={help}
            emptyContent={isWaiting || !typed ? null : undefined}
            items={choices}
            label={label}
            onInputValueChange={(text) => {
                setTyped(text);
                debouncedSearch(text);
            }}
            onValueChange={(choice) => onChange(choice ? choice.value : null)}
            placeholder={placeholder || _x('Search...', 'admin-text', 'site-reviews')}
            showClearButton
            statusContent={isWaiting ? <LoadingIndicator /> : (
                <VisuallyHidden>
                    {sprintf(
                        /* translators: %d: the number of items found */
                        _nx('%d result', '%d results', choices.length, 'admin-text', 'site-reviews'),
                        choices.length
                    )}
                </VisuallyHidden>
            )}
            value={selected}
        />
    );
};

export default AjaxSearchControl;
