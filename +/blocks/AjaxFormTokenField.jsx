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
const AjaxFormTokenField = ({ endpoint, onChange, placeholder, value, ...extraProps }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [options, setOptions] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const abortControllerRef = useRef(null); // Ref to track the AbortController
    // const { setOptions } = useDispatch('site-reviews');

    // Retrieve options from the cache
    // const options = useSelect(
    //     (select) => select('site-reviews').getOptions(endpointUrl),
    //     [endpointUrl]
    // );

    // Update the endpoint URL when the search query or value array changes
    const endpointUrl = useMemo(() => {
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set('include', value.join(','));
        url.searchParams.set('search', searchQuery);
        return url.pathname + url.search;
    }, [searchQuery, value]);

    // Preprocess options into a dictionary for quick lookups
    const optionsMap = options?.reduce((map, option) => {
        map[option.displayTitle] = option;
        return map;
    }, {});

    // Fetch the suggestions from the endpoint
    const fetchSuggestions = async () => {
        // if (options && options.length > 0) return;
        setIsLoading(true)
        try {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort(); // Cancel any previous request
            }
            // Create a new AbortController instance for this fetch
            const controller = new AbortController();
            abortControllerRef.current = controller;
            const response = await apiFetch({
                path: endpointUrl,
                signal: controller.signal, // Pass the signal to fetch to cancel if needed
            });
            // Only update the state if the request is not aborted
            if (controller.signal.aborted) return;

            if (!Array.isArray(response) || response.length === 0) {
                throw new Error('Invalid or empty response from API');
            }
            // Detect duplicate titles
            const titleCounts = response.reduce((counts, item) => {
                counts[item.title] = (counts[item.title] || 0) + 1;
                return counts;
            }, {});
            // Append ID only to duplicate titles
            const processedOptions = response.map((option) => ({
                ...option,
                displayTitle: titleCounts[option.title] > 1
                    ? `${option.id}: ${option.title}`
                    : option.title,
            }));
            setOptions(processedOptions);
            // setOptions(endpointUrl, processedOptions);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error fetching options', error);
            }
        } finally {
            if (!abortControllerRef.current.signal.aborted) {
                setIsLoading(false)
            }
        }
    };

    // Convert selected IDs to their titles for the FormTokenField
    const getTokenTitles = () => {
        if (!Array.isArray(options)) return [];
        return value
            .map((id) => {
                const item = options.find((option) => option.id === id);
                return item ? item.displayTitle : '';
            })
            .filter((title) => '' !== title);
    };

    // Handle token field changes
    const handleTokenChange = (tokens) => {
        const selectedIds = tokens
            .map((token) => optionsMap?.[token]?.id || null)
            .filter((id) => id !== null);
        onChange(selectedIds);
    };

    const computeSuggestionMatch = (suggestion) => {
        const matchText = searchQuery.toLocaleLowerCase();
        if (matchText.length === 0) {
            return null;
        }
        const indexOfMatch = suggestion.toLocaleLowerCase().indexOf(matchText);
        return {
            suggestionBeforeMatch: suggestion.substring(0, indexOfMatch),
            suggestionMatch: suggestion.substring(indexOfMatch, indexOfMatch + matchText.length),
            suggestionAfterMatch: suggestion.substring(indexOfMatch + matchText.length),
        }
    };

    const renderItem = ({ item }) => {
        // Ensure options is available before calling .find
        if (!options || !Array.isArray(options)) return null;

        const { id, title } = options.find((option) => option.displayTitle === item);
        const matchText = computeSuggestionMatch(title);
        return (
            <HStack>
                {matchText ? (
                    <span aria-label={title}>
                        { matchText.suggestionBeforeMatch }
                        <strong className="components-form-token-field__suggestion-match">
                            { matchText.suggestionMatch }
                        </strong>
                        { matchText.suggestionAfterMatch }
                    </span>
                ) : (
                    <Text color="inherit">{title || item}</Text>
                )}
                <Text color="inherit" size="small" style={{ opacity: '0.5' }}>{String(id)}</Text>
            </HStack>
        )
    };

    // Exclude critical props from extraProps
    const safeProps = Object.fromEntries(
        Object.entries(extraProps).filter(([key]) => ![
            'onChange',
            'placeholder',
            'value',
        ].includes(key))
    );

    // Fetch suggestions whenever endpointUrl changes
    useEffect(() => {
        fetchSuggestions()
        return () => {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort(); // Abort any ongoing request when unmounting
            }
        };
    }, [endpointUrl]);

    return (
        <BaseControl __nextHasNoMarginBottom>
            <FormTokenField
                __experimentalExpandOnFocus
                __experimentalRenderItem={renderItem}
                __experimentalShowHowTo={false}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={handleTokenChange}
                onInputChange={setSearchQuery}
                placeholder={isLoading
                    ? _x('Loading...', 'admin-text', 'site-reviews')
                    : (placeholder || _x('Select...', 'admin-text', 'site-reviews'))}
                suggestions={options 
                    ? options.map((option) => option.displayTitle) 
                    : []}
                value={options 
                    ? getTokenTitles() 
                    : []}
                {...safeProps}
            />
        </BaseControl>
    )
};

export default AjaxFormTokenField;
