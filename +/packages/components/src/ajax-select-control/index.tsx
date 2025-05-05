import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _x } from '@wordpress/i18n';
import { Animate, BaseControl, SelectControl } from '@wordpress/components';
import { ControlProps, Item, Option } from './types';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

const AjaxSelectControl = (props: ControlProps) => {
    const {
        endpoint,
        fallback = null,
        hideIfEmpty = false,
        options: _, // discard
        placeholder,
        storeName = DEFAULT_STORE_NAME,
        ...controlProps
    } = props;

    const [isLoading, setIsLoading] = useState<boolean>(false);

    const registeredStoreName: string = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).getOptions(endpoint) || [],
        [endpoint, registeredStoreName]
    );
    const { setOptions } = useDispatch(registeredStoreName);

    useEffect(() => {
        if (options.length) return;
        setIsLoading(true);
        apiFetch({ path: endpoint }).then((response: Item[]) => {
            const newOptions: Option[] = response.map((item) => ({
                label: item.title || String(item.id),
                value: String(item.id),
            }));
            setOptions(endpoint, newOptions);
        })
        .catch((error) => {
            console.error('Error fetching options:', error);
        })
        .finally(() => {
            setIsLoading(false);
        })
    }, [endpoint]);

    if (0 === options.length && (fallback || hideIfEmpty)) {
        return isLoading ? null : fallback;
    }

    return (
        <Animate type={isLoading ? 'loading' : undefined}>
            {({ className }) => (
                <BaseControl __nextHasNoMarginBottom>
                    <SelectControl
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        className={className}
                        disabled={isLoading}
                        options={[
                            {
                                label: placeholder || _x('Select...', 'admin-text', 'site-reviews'),
                                value: '',
                            },
                            ...options,
                        ]}
                        {...controlProps}
                    />
                </BaseControl>
            )}
        </Animate>
    );
};

export default AjaxSelectControl;
