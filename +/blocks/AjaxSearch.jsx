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
 * <AjaxFormTokenField
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

    const debouncedSearch = useDebounce((searchText) => {
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

    const performSearch = async () => {
        console.log('performSearch')
        setIsSearching(true)
        return apiFetch({ path: endpointUrl }).then((response) => {
            const localSuggestions = [];
            response.forEach((item) => {
                localSuggestions.push({
                    id: item.id,
                    value: item.title + ' (' + item.id + ')',
                });
            });
            setSuggestedValues(localSuggestions);
            setIsSearching(false)
        });
    };

    const handleValueChange = (nextValues) => {
        nextValues.map((value, index) => {
            if (typeof value === 'string') {
                // This is a new entry, we need to replace the string with an object.
                // Find the user suggestion that has the same label as the value.
                const suggestedValue = suggestedValues.find((suggestion) => suggestion.value === value);
                if (suggestedValue) {
                    nextValues[index] = suggestedValue;
                }
            } else {
                // This is an existing entry that already is an object with id and label.
                // No need to do anything.
            }
            return value;
        });
        setSelectedValues(nextValues);
        onChange(nextValues.map((selected) => selected.id));
    };

    // const computeSuggestionMatch = (suggestion) => {
    //     const matchText = search.toLocaleLowerCase();
    //     if (matchText.length === 0) {
    //         return null;
    //     }
    //     const indexOfMatch = suggestion.toLocaleLowerCase().indexOf(matchText);
    //     return {
    //         suggestionBeforeMatch: suggestion.substring(0, indexOfMatch),
    //         suggestionMatch: suggestion.substring(indexOfMatch, indexOfMatch + matchText.length),
    //         suggestionAfterMatch: suggestion.substring(indexOfMatch + matchText.length),
    //     }
    // };

    // const renderItem = ({ item }) => {
    //     // Ensure options is available before calling .find
    //     if (!options || !Array.isArray(options)) {
    //         return (
    //             <Text color="inherit">Loading...</Text>
    //         );
    //     }

    //     const { id, title } = options.find((option) => option.displayTitle === item);
    //     const matchText = computeSuggestionMatch(title);
    //     return (
    //         <HStack>
    //             {matchText ? (
    //                 <span aria-label={title}>
    //                     { matchText.suggestionBeforeMatch }
    //                     <strong className="components-form-token-field__suggestion-match">
    //                         { matchText.suggestionMatch }
    //                     </strong>
    //                     { matchText.suggestionAfterMatch }
    //                 </span>
    //             ) : (
    //                 <Text color="inherit">{title || item}</Text>
    //             )}
    //             <Text color="inherit" size="small" style={{ opacity: '0.5' }}>{String(id)}</Text>
    //         </HStack>
    //     )
    // };


    // Effect runs only once on mount due to empty dependency array
    useEffect(() => {
        // Check if we've already fetched the data
        if (hasFetchedData.current || 0 === value.length) return;
        console.log('initFetch', value)
        apiFetch({ path: endpointUrl }).then(response => {
            console.log('initial fetch')
            hasFetchedData.current = true; // Mark that we've fetched the data
            const initialValues = [];
            response.forEach((item) => {
                initialValues.push({
                    id: item.id,
                    value: item.title + ' (' + item.id + ')',
                })
            })
            setSelectedValues(initialValues)
        })
    }, [])

    // Fetch suggestions whenever endpointUrl changes
    useEffect(() => {
        if (!hasFetchedData.current) return;
        performSearch()
    }, [endpointUrl]);

    return (
        <BaseControl __nextHasNoMarginBottom>
            <FormTokenField
                __experimentalAutoSelectFirstMatch
                __experimentalExpandOnFocus
                // __experimentalRenderItem={renderItem}
                __experimentalShowHowTo={ false }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                label={ label }
                onChange={ handleValueChange }
                onInputChange={ debouncedSearch }
                placeholder={ placeholder || _x('Select...', 'admin-text', 'site-reviews') }
                suggestions={ suggestedValues.map((suggestion) => suggestion.value) }
                value={ selectedValues }
            />
        </BaseControl>
    )
};

export default AjaxSearch;
