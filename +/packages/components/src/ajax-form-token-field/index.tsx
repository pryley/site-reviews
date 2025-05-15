import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    Animate,
    BaseControl,
    FormTokenField,
} from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { ControlProps, Item, TransformedItem, SuggestionMatch } from './types';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useMemo, useRef, useState } from '@wordpress/element';

const AjaxFormTokenField = (props: ControlProps) => {
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

    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [isSearching, setIsSearching] = useState<boolean>(false);
    const [search, setSearch] = useState<string>('');
    const [suggestionMap, setSuggestionMap] = useState<Map<string, TransformedItem>>(new Map());
    const hasFetchedData = useRef<boolean>(false);

    const registeredStoreName: string = createStore(storeName);
    const selectedValues = useSelect<TransformedItem[]>(
        (select) => select(registeredStoreName).getSelectedValues(endpoint),
        []
    );
    const suggestedValues = useSelect<TransformedItem[]>(
        (select) => select(registeredStoreName).getSuggestedValues(endpoint),
        []
    );
    const { setSelectedValues, setSuggestedValues } = useDispatch(registeredStoreName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value.join(','),
            search,
        }),
    });

    const transformItem = (item: Item): TransformedItem => ({
        id: String(item.id),
        title: item.title,
        value: !isNaN(parseFloat(item.id)) ? `${item.id}: ${item.title}` : item.title,
    });

    const initValues = async () => {
        if (hasFetchedData.current) return;
        if (suggestedValues.length || (!value.length && !prefetch)) {
            hasFetchedData.current = true; // Mark that we've fetched the data
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
                if (value.includes(item.id)) {
                    initialValues.push(transformed);
                }
            });
            setSelectedValues(endpoint, initialValues);
            setSuggestedValues(endpoint, initialSuggestions);
        } finally {
            setIsLoading(false);
        }
    };

    const performSearch = async () => {
        if (search.length < 2) return;
        setIsSearching(true);
        try {
            const response = await apiFetch<Item[]>(req());
            setSuggestedValues(endpoint, response.map(transformItem));
        } finally {
            setIsSearching(false);
        }
    };

    const handleValueChange = (nextValues: (string | TransformedItem)[]) => {
        nextValues.map((nextValue, index) => {
            // If nextValue is a string then it is a new entry and we need to replace with an object.
            if (typeof nextValue === 'string') {
                const suggestedValue = suggestedValues.find((suggestion) => suggestion.value === nextValue);
                if (suggestedValue) {
                    nextValues[index] = suggestedValue;
                }
            }
            return nextValue;
        });
        setSearch('')
        setSelectedValues(endpoint, nextValues)
        onChange(nextValues.map((selected) => selected.id))
    };

    const computeSuggestionMatch = (suggestion: string): SuggestionMatch | null => {
        const matchText = search.toLocaleLowerCase();
        if (matchText.length === 0) {
            return null;
        }
        const indexOfMatch = suggestion.toLocaleLowerCase().indexOf(matchText);
        return {
            afterMatch: suggestion.substring(indexOfMatch + matchText.length),
            beforeMatch: suggestion.substring(0, indexOfMatch),
            match: suggestion.substring(indexOfMatch, indexOfMatch + matchText.length),
        };
    };

    const renderItem = ({ item }: { item: string }) => {
        const suggestion = suggestionMap.get(item);
        if (!suggestion) return null; // Item not found
        const { id, title } = suggestion;
        const matchText = computeSuggestionMatch(title);
        return (
            <HStack>
                {matchText ? (
                    <Text color="inherit" numberOfLines={1} aria-label={title}>
                        {matchText.beforeMatch}
                        <strong className="components-form-token-field__suggestion-match">
                            {matchText.match}
                        </strong>
                        {matchText.afterMatch}
                    </Text>
                ) : (
                    <Text color="inherit">{title}</Text>
                )}
                <Text color="inherit" size="small" style={{ flexShrink: 0, opacity: '0.5' }}>
                    {id}
                </Text>
            </HStack>
        );
    };

    const validateInput = (input: string): boolean => {
        return suggestedValues.some((item) => item.value === input);
    }

    const debouncedSearch = useDebounce(performSearch, 250);

    const expandOnFocus = useMemo(
        () => {
            const hasUniqueSuggestions = suggestedValues.some(
                (suggestion) => !selectedValues.some((selected) => selected.id === suggestion.id)
            );
            return search.length > 1 || hasUniqueSuggestions;
        },
        [search, selectedValues, suggestedValues]
    );

    // Run only on mount
    useEffect(() => { initValues() }, [])

    // Fetch suggestions whenever search changes
    useEffect(() => { debouncedSearch() }, [search])

    // Use a Map for faster lookups when rendering suggestions
    useEffect(() => {
        setSuggestionMap(new Map(suggestedValues.map((item) => [item.value, item])));
    }, [suggestedValues]);

    return (
        <BaseControl __nextHasNoMarginBottom>
            <Animate type={(isLoading || isSearching) ? 'loading' : undefined}>
                {({ className }) => (
                    <FormTokenField
                        __experimentalExpandOnFocus={expandOnFocus}
                        __experimentalRenderItem={renderItem}
                        __experimentalShowHowTo={false}
                        __experimentalValidateInput={validateInput}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        className={className}
                        disabled={isLoading}
                        label={label || ''}
                        onChange={handleValueChange}
                        onInputChange={setSearch}
                        placeholder={
                            placeholder || _x('Search...', 'admin-text', 'site-reviews')
                        }
                        suggestions={suggestedValues.map((suggestion) => suggestion.value)}
                        value={selectedValues}
                    />
                )}
            </Animate>
            {help && (
                <Text variant="muted" size="small">
                    {help}
                </Text>
            )}
        </BaseControl>
    );
};

export default AjaxFormTokenField;
