import apiFetch from '@wordpress/api-fetch';
import { BaseControl, ToggleControl, Spinner, useBaseControlProps } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * <AjaxToggleGroupControl
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=hide"
 *     key="hide"
 *     onChange={(hide) => setAttributes({ hide })}
 *     value={attributes.hide}
 * />
 * 
 * @version 1.0
 */
const AjaxToggleGroupControl = ({ endpoint, onChange, value = [], ...extraProps }) => {
    const [isLoading, setIsLoading] = useState(false);
    const { baseControlProps, controlProps } = useBaseControlProps(extraProps);
    const { setOptions } = useDispatch('site-reviews');

    // Retrieve options from the cache
    const options = useSelect(
        (select) => select('site-reviews').getOptions(endpoint),
        []
    );

    // Handle checkbox changes
    const handleCheckboxChange = (optionValue, isChecked) => {
        const newValue = isChecked
            ? [...value, optionValue]
            : value.filter((v) => v !== optionValue);
        onChange(newValue);
    };

    // Exclude critical props from controlProps
    const safeProps = Object.fromEntries(
        Object.entries(controlProps).filter(([key]) => ![
            'label',
            'checked',
            'onChange',
            'value',
        ].includes(key))
    );

    useEffect(() => {
        if (options.length) {
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
                console.error('Error fetching options:', error);
            } finally {
                setIsLoading(false);
            }
        })();
    }, []);

    return (
        <BaseControl __nextHasNoMarginBottom {...baseControlProps}>
            {isLoading && <Spinner />}
            {!isLoading && options && options.map((option) => (
                <ToggleControl
                    __nextHasNoMarginBottom
                    key={option.value}
                    label={option.label}
                    checked={value.includes(option.value)}
                    onChange={(isChecked) => handleCheckboxChange(option.value, isChecked)}
                    {...safeProps}
                />
            ))}
        </BaseControl>
    )
};

export default AjaxToggleGroupControl;
