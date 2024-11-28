import apiFetch from '@wordpress/api-fetch';
import { _x } from '@wordpress/i18n';
import { BaseControl, FormTokenField, Spinner, useBaseControlProps } from '@wordpress/components';
import { useState, useEffect, useRef } from '@wordpress/element';

/**
 * <AjaxFormTokenField
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=assigned_posts"
 *     key="assigned_posts"
 *     onChange={(assigned_posts) => setAttributes({ assigned_posts })}
 *     value={attributes.assigned_posts}
 * />
 * 
 * @version 1.0
 */
const AjaxFormTokenField = ({ endpoint, onChange, placeholder, value, ...extraProps }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [options, setOptions] = useState([]);
    const isMounted = useRef(true);  // Mount tracking to prevent state updates after unmount

    // Exclude critical props from extraProps
    const safeProps = Object.fromEntries(
        Object.entries(extraProps).filter(([key]) => ![
            'onChange',
            'placeholder',
            'value',
        ].includes(key))
    );

    // Fetch the suggestions from the endpoint
    const fetchSuggestions = async (query = '') => {
        setIsLoading(true)
        try {
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('include', value.join(','))
            url.searchParams.set('search', query)
            const response = await apiFetch({ path: url.pathname + url.search });
            if (isMounted.current) {
                if (!Array.isArray(response) || response.length === 0) {
                    throw new Error('Invalid or empty response from API');
                }
                setOptions(response)
            }
        } catch (error) {
            console.error('Error fetching options:', error)
        } finally {
            if (isMounted.current) {
                setIsLoading(false)
            }
        }
    };

    // Convert selected post IDs to their titles for the FormTokenField
    const getTokenTitles = () => {
        return value
            .map((id) => {
                const item = options.find((option) => option.id === id);
                return item ? item.title : '';
            })
            .filter((title) => '' !== title);
    };

    // Handle token field changes
    const handleTokenChange = (tokens) => {
        const selectedIds = tokens
            .map((token) => {
                const item = options.find((option) => option.title === token);
                return item ? item.id : null;
            })
            .filter((id) => null !== id);
        onChange(selectedIds);
    };

    useEffect(() => {
        fetchSuggestions()
        return () => {
            isMounted.current = false;
        };
    }, [])

    return (
        <BaseControl __nextHasNoMarginBottom>
            <FormTokenField
                __experimentalExpandOnFocus
                __experimentalShowHowTo={false}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                onChange={handleTokenChange}
                onInputChange={fetchSuggestions}
                placeholder={isLoading
                    ? _x('Loading...', 'admin-text', 'site-reviews')
                    : (placeholder || _x('Select...', 'admin-text', 'site-reviews'))
                }
                suggestions={options.map((option) => option.title)}
                value={getTokenTitles()}
                {...safeProps}
            />
        </BaseControl>
    )
};

export default AjaxFormTokenField;
