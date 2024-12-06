import apiFetch from '@wordpress/api-fetch';
import storeName from './Store';
import { BaseControl, ToggleControl, Spinner, useBaseControlProps } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * <AjaxToggleGroupControl
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=hide"
 *     onChange={(hide) => setAttributes({ hide })}
 *     value={attributes.hide}
 * />
 * 
 * @version 1.0
 */
const AjaxToggleGroupControl = ({ endpoint, onChange, value, ...props }) => {
    const [isLoading, setIsLoading] = useState(false);
    const options = useSelect(select => select(storeName).getOptions(endpoint), []);
    const { baseControlProps, controlProps } = useBaseControlProps(props);
    const { checked: _, label: __, ...extraProps } = controlProps;
    const { setOptions } = useDispatch(storeName);

    const handleIsChecked = (optionValue, isChecked) => {
        const newValue = isChecked
            ? [...value, optionValue]
            : value.filter((v) => v !== optionValue);
        onChange(newValue);
    };

    useEffect(() => {
        if (options.length) return;
        setIsLoading(true)
        apiFetch({ path: endpoint }).then(response => {
            const initialOptions = response.map((item) => ({
                label: item.title,
                value: String(item.id),
            }));
            setOptions(endpoint, initialOptions)
        }).finally(() => {
            setIsLoading(false)
        })
    }, []);

    return (
        <BaseControl __nextHasNoMarginBottom {...baseControlProps}>
            { !isLoading && options.map(option => (
                <ToggleControl
                    __nextHasNoMarginBottom
                    key={ option.value }
                    label={ option.label }
                    checked={ value.includes(option.value) }
                    onChange={ (isChecked) => handleIsChecked(option.value, isChecked) }
                    { ...extraProps }
                />
            )) }
            { isLoading && <Spinner /> }
        </BaseControl>
    )
};

export default AjaxToggleGroupControl;
