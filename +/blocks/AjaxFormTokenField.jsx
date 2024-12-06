import apiFetch from '@wordpress/api-fetch';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    BaseControl,
    FormTokenField,
} from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useRef, useState } from '@wordpress/element';

/**
 * <AjaxFormTokenField
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=assigned_posts"
 *     key="assigned_posts"
 *     onChange={(assigned_posts) => setAttributes({ assigned_posts })}
 *     value={attributes.assigned_posts}
 * />
 * 
 * @version 1.2
 */
const AjaxFormTokenField = ({ endpoint, label, onChange, placeholder, value }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [search, setSearch] = useState('');
    const [selectedValues, setSelectedValues] = useState([]);
    const [suggestedValues, setSuggestedValues] = useState([]);
    const hasFetchedData = useRef(false);

    // const selectedValues = useSelect(
    //     select => select('site-reviews').getSelectedValues(endpoint),
    //     [value]
    // );
    // const {setSelectedValues} = useDispatch('site-reviews');

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value.join(','),
            search,
        }),
    });

    const debouncedSearch = useDebounce(searchText => {
        if (searchText.length > 1) {
            setSearch(searchText)
        }
    }, 500);

    const transformItem = (item) => ({
        id: item.id,
        title: item.title,
        value: (!isNaN(parseFloat(item.id)) ? `${item.title} (${item.id})` : item.title),
    });

    const initValues = async () => {
        if (hasFetchedData.current) return
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
            setSelectedValues(initialValues)
            setSuggestedValues(initialSuggestions)
            // setSelectedValues(endpoint, initialValues) // Store initial values for this endpoint
            setIsLoading(false)
        })
    };

    const performSearch = async () => {
        if (!hasFetchedData.current) return
        setIsSearching(true)
        apiFetch(req()).then(response => {
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
        setSelectedValues(nextValues)
        // setSelectedValues(endpoint, nextValues); // Update the store with the endpoint-specific values
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
        const { id, title, value } = suggestedValues.find(suggestion => suggestion.value === item) || {};
        if (!id) return null; // In case we can't find an item
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

    const validateInput = (input) => {
        return suggestedValues.some(item => item.value === input)
    };

    // Runs only once on mount due to empty dependency array
    useEffect(() => { initValues() }, [])

    // Fetch suggestions whenever search changes
    useEffect(() => { performSearch() }, [search])

    return (
        <BaseControl __nextHasNoMarginBottom>
            <FormTokenField
                __experimentalAutoSelectFirstMatch
                __experimentalExpandOnFocus
                __experimentalRenderItem={ renderItem }
                __experimentalShowHowTo={ false }
                __experimentalValidateInput={ validateInput }
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                label={ label || '' }
                onChange={ handleValueChange }
                onInputChange={ debouncedSearch }
                placeholder={ placeholder || _x('Search...', 'admin-text', 'site-reviews') }
                suggestions={ suggestedValues.map(suggestion => suggestion.value) }
                value={ selectedValues }
            />
            {isSearching && (
                <Text variant="muted" size="small">{ _x('Searching...', 'admin-text', 'site-reviews') }</Text>
            )}
        </BaseControl>
    )
};

export default AjaxFormTokenField;
