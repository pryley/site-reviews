import apiFetch from '@wordpress/api-fetch';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    BaseControl,
    FormTokenField,
} from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useMemo, useRef, useState } from '@wordpress/element';

/**
 * <AjaxSearch
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=assigned_posts"
 *     key="assigned_posts"
 *     onChange={(assigned_posts) => setAttributes({ assigned_posts })}
 *     value={attributes.assigned_posts}
 * />
 * 
 * @version 1.1
 */
const AjaxSearch = ({ endpoint, label, onChange, placeholder, value }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [search, setSearch] = useState('');
    const [selectedValues, setSelectedValues] = useState([]);
    const [suggestedValues, setSuggestedValues] = useState([]);
    const hasFetchedData = useRef(false);

    const debouncedSearch = useDebounce(searchText => {
        if (searchText.length > 1) {
            setSearch(searchText)
        }
    }, 500);

    // Update the endpoint URL when the search query or value array changes
    const endpointUrl = useMemo(() => {
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set('include', value.join(','));
        url.searchParams.set('search', search);
        return url.pathname + url.search;
    }, [search, value]);

    const transformItem = (item) => ({
        id: item.id,
        title: item.title,
        value: item.title + ' (' + item.id + ')',
    });

    const initValues = async () => {
        if (hasFetchedData.current || 0 === value.length) return
        setIsLoading(true)
        apiFetch({ path: endpointUrl }).then(response => {
            hasFetchedData.current = true; // Mark that we've fetched the data
            const initialValues = [];
            response.forEach(item => initialValues.push(transformItem(item)))
            setSelectedValues(initialValues)
            setIsLoading(false)
        })
    };

    const performSearch = async () => {
        if (!hasFetchedData.current) return
        setIsSearching(true)
        return apiFetch({ path: endpointUrl }).then(response => {
            const suggestedResults = [];
            response.forEach(item => suggestedResults.push(transformItem(item)))
            setSuggestedValues(suggestedResults)
            setIsSearching(false)
        })
    };

    const handleValueChange = (nextValues) => {
        nextValues.map((value, index) => {
            // If value is a string then it is a new entry and we need to replace with an object.
            if (typeof value === 'string') {
                const suggestedValue = suggestedValues.find(suggestion => suggestion.value === value);
                if (suggestedValue) {
                    nextValues[index] = suggestedValue;
                }
            }
            return value;
        });
        setSelectedValues(nextValues);
        onChange(nextValues.map(selected => selected.id));
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
        const { id, title, value } = suggestedValues.find(suggestion => suggestion.value === item);
        const matchText = computeSuggestionMatch(title);
        return (
            <HStack>
                {matchText ? (
                    <span aria-label={title}>
                        { matchText.beforeMatch }
                        <strong className="components-form-token-field__suggestion-match">
                            { matchText.match }
                        </strong>
                        { matchText.afterMatch }
                    </span>
                ) : (
                    <Text color="inherit">{title}</Text>
                )}
                <Text color="inherit" size="small" style={{ opacity: '0.5' }}>{String(id)}</Text>
            </HStack>
        )
    };

    // Runs only once on mount due to empty dependency array
    useEffect(() => { initValues() }, [])

    // Fetch suggestions whenever endpointUrl changes
    useEffect(() => { performSearch() }, [endpointUrl])

    return (
        <BaseControl __nextHasNoMarginBottom>
            <FormTokenField
                __experimentalAutoSelectFirstMatch
                __experimentalExpandOnFocus
                __experimentalRenderItem={ renderItem }
                __experimentalShowHowTo={ false }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                label={ label }
                onChange={ handleValueChange }
                onInputChange={ debouncedSearch }
                placeholder={ placeholder || _x('Select...', 'admin-text', 'site-reviews') }
                suggestions={ suggestedValues.map(suggestion => suggestion.value) }
                value={ selectedValues }
            />
        </BaseControl>
    )
};

export default AjaxSearch;
