import apiFetch from '@wordpress/api-fetch';
import storeName from './Store';
import { _x } from '@wordpress/i18n';
import { Animate, BaseControl, ComboboxControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * <AjaxComboboxControl
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=type"
 *     onChange={(type) => setAttributes({ type })}
 *     value={attributes.type}
 * />
 * 
 * @version 1.0
 */
const AjaxComboboxControl = ({ endpoint, hideIfEmpty = false, placeholder, ...props }) => {
    const [isLoading, setIsLoading] = useState(false);
    const options = useSelect(select => select(storeName).getOptions(endpoint), []);
    const { options: _, ...extraProps } = props;
    const { setOptions } = useDispatch(storeName);

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
    }, [])

    return (
        <>
        { (!hideIfEmpty || options.length > 1) && (
            <Animate type={ isLoading && 'loading' }>
                { ({ className }) => (
                    <BaseControl __nextHasNoMarginBottom>
                        <ComboboxControl
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                            allowReset
                            className={ className }
                            expandOnFocus={ false }
                            options={ options }
                            placeholder={ placeholder || _x('Select...', 'admin-text', 'site-reviews') }
                            { ...extraProps }
                        />
                    </BaseControl>
                ) }
            </Animate>
        ) }
        </>
    )
};

export default AjaxComboboxControl;
