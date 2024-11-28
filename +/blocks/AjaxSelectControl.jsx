import apiFetch from '@wordpress/api-fetch';
import { _x } from '@wordpress/i18n';
import { BaseControl, SelectControl, useBaseControlProps } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * <AjaxSelectControl
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=type"
 *     key="type"
 *     onChange={(type) => setAttributes({ type })}
 *     value={attributes.type}
 * />
 * 
 * @version 1.0
 */
const AjaxSelectControl = ({ endpoint, placeholder, ...extraProps }) => {
    const [isLoading, setIsLoading] = useState(false);
    const { baseControlProps, controlProps } = useBaseControlProps(extraProps);
    const { setOptions } = useDispatch('site-reviews');

    // Retrieve options from the cache
    const options = useSelect(
        (select) => select('site-reviews').getOptions(endpoint),
        [endpoint]
    );

    // Exclude critical props from controlProps
    const safeProps = Object.fromEntries(
        Object.entries(controlProps).filter(([key]) => ![
            'options',
            'placeholder',
        ].includes(key))
    );

    useEffect(() => {
        if (options) {
            return; // Options are already cached, no need to fetch
        }
        (async () => {
            setIsLoading(true);
            try {
                const url = new URL(endpoint, window.location.origin);
                const response = await apiFetch({ path: url.pathname + url.search });
                if (!Array.isArray(response) || response.length === 0) {
                    throw new Error('Invalid or empty response from API');
                }
                const fetchedOptions = response.map((item) => ({
                    label: item.title,
                    value: item.id,
                }));
                // Cache the fetched options in the store
                setOptions(endpoint, fetchedOptions);
            } catch (error) {
                console.error('Error fetching options', error);
            } finally {
                setIsLoading(false);
            }
        })();
    }, [endpoint, options, setOptions]);

    return (
        <BaseControl __nextHasNoMarginBottom {...baseControlProps}>
            <SelectControl
                __next40pxDefaultSize
                __nextHasNoMarginBottom
                allowReset
                disabled={isLoading}
                options={isLoading || !options
                    ? [{ label: _x('Loading...', 'admin-text', 'site-reviews'), value: '' }]
                    : [{ label: (placeholder || _x('Select...', 'admin-text', 'site-reviews')), value: '' }, ...options]
                }
                {...safeProps}
            />
        </BaseControl>
    )
};

export default AjaxSelectControl;
