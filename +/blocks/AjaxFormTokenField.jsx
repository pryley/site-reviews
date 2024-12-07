import apiFetch from '@wordpress/api-fetch';
import storeName from './Store';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    Animate,
    BaseControl,
    FormTokenField,
} from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useRef, useState } from '@wordpress/element';

/**
 * <AjaxFormTokenField
 *     endpoint="/site-reviews/v1/shortcode/site_reviews?option=assigned_posts"
 *     onChange={(assigned_posts) => setAttributes({ assigned_posts })}
*      prefetch={ true }
 *     value={attributes.assigned_posts}
 * />
 * 
 * @version 1.0
 */
const AjaxFormTokenField = ({ endpoint, help, label, onChange, placeholder, prefetch = false, value }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [search, setSearch] = useState('');
    const [suggestionMap, setSuggestionMap] = useState(new Map());
    const hasFetchedData = useRef(false);
    const isFirstRun = useRef(true);
    const selectedValues = useSelect(select => select(storeName).getSelectedValues(endpoint), []);
    const suggestedValues = useSelect(select => select(storeName).getSuggestedValues(endpoint), []);
    const { setSelectedValues, setSuggestedValues } = useDispatch(storeName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value.join(','),
            search,
        }),
    });

    const debouncedSearch = useDebounce(setSearch, 250);

    const transformItem = (item) => ({
        id: item.id,
        title: item.title,
        value: (!isNaN(parseFloat(item.id)) ? `${item.title} (${item.id})` : item.title),
    });

    const initValues = async () => {
        if (hasFetchedData.current) return
        if (suggestedValues.length || (!value.length && prefetch === false)) {
            hasFetchedData.current = true; // Mark that we've fetched the data
            return
        }
        setIsLoading(true)
        apiFetch(req()).then(response => {
            hasFetchedData.current = true; // Mark that we've fetched the data
            const initialSuggestions = [];
            const initialValues = [];
            response.forEach(item => {
                initialSuggestions.push(transformItem(item))
                if (value.includes(item.id)) {
                    initialValues.push(transformItem(item))
                }
            })
            setSelectedValues(endpoint, initialValues)
            setSuggestedValues(endpoint, initialSuggestions)
        }).finally(() => {
            setIsLoading(false)
        })
    };

    const performSearch = async () => {
        if (search.length < 2) {
            return
        }
        if (isFirstRun.current) {
            isFirstRun.current = false;
            return
        }
        setIsSearching(true)
        apiFetch(req()).then(response => {
            setSuggestedValues(endpoint, response.map(transformItem))
        }).finally(() => {
            setIsSearching(false)
        })
    };

    const handleValueChange = (nextValues) => {
        nextValues.map((nextValue, index) => {
            // If nextValue is a string then it is a new entry and we need to replace with an object.
            if (typeof nextValue === 'string') {
                const suggestedValue = suggestedValues.find(suggestion => suggestion.value === nextValue);
                if (suggestedValue) {
                    nextValues[index] = suggestedValue;
                }
            }
            return nextValue;
        });
        setSelectedValues(endpoint, nextValues)
        onChange(nextValues.map(selected => selected.id))
    };

    const computeSuggestionMatch = (suggestion) => {
        const matchText = search.toLocaleLowerCase();
        if (matchText.length === 0) {
            return null;
        }
        const indexOfMatch = suggestion.toLocaleLowerCase().indexOf(matchText);
        return {
            afterMatch: suggestion.substring(indexOfMatch + matchText.length),
            beforeMatch: suggestion.substring(0, indexOfMatch),
            match: suggestion.substring(indexOfMatch, indexOfMatch + matchText.length),
        }
    };

    const renderItem = ({ item }) => {
        const suggestion = suggestionMap.get(item);
        if (!suggestion) return null; // Item not found
        const { id, title } = suggestion;
        const matchText = computeSuggestionMatch(title);
        return (
            <HStack>
                { matchText ? (
                    <span aria-label={ title }>
                        { matchText.beforeMatch }
                        <strong className="components-form-token-field__suggestion-match">
                            { matchText.match }
                        </strong>
                        { matchText.afterMatch }
                    </span>
                ) : (
                    <Text color="inherit">{ title }</Text>
                ) }
                <Text color="inherit" size="small" style={{ opacity: '0.5' }}>{ String(id) }</Text>
            </HStack>
        )
    };

    const validateInput = (input) => {
        return suggestedValues.some(item => item.value === input)
    };

    // Run only on mount
    useEffect(() => { initValues() }, [])

    // Fetch suggestions whenever search changes
    useEffect(() => { performSearch() }, [search])

    // Use a Map for faster lookups when rendering suggestions
    useEffect(() => {
        setSuggestionMap(new Map(suggestedValues.map(item => [item.value, item])));
    }, [suggestedValues]);

    return (
        <BaseControl __nextHasNoMarginBottom>
            <Animate type={ (isLoading || isSearching) && 'loading' }>
                { ({ className }) => (
                    <FormTokenField
                        __experimentalExpandOnFocus
                        __experimentalRenderItem={ renderItem }
                        __experimentalShowHowTo={ false }
                        __experimentalValidateInput={ validateInput }
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        className={ className }
                        disabled={ isLoading }
                        label={ label || '' }
                        onChange={ handleValueChange }
                        onInputChange={ debouncedSearch }
                        placeholder={ placeholder || _x('Search...', 'admin-text', 'site-reviews') }
                        suggestions={ suggestedValues.map(suggestion => suggestion.value) }
                        value={ selectedValues }
                    />
                ) }
            </Animate>
            { help && (
                <Text variant="muted" size="small">{ help }</Text>
            ) }
        </BaseControl>
    )
};

export default AjaxFormTokenField;
