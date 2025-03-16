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
        hideIfEmpty = false,
        options: _,
        placeholder,
        storeName = DEFAULT_STORE_NAME,
        ...controlProps
    } = props;

    const [isLoading, setIsLoading] = useState<boolean>(false);

    const registeredStoreName = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).getOptions(endpoint),
        []
    );
    const { setOptions } = useDispatch(registeredStoreName);

    useEffect(() => {
        if (options.length) return;
        setIsLoading(true);
        apiFetch<Item[]>({ path: endpoint })
            .then((response) => {
                const initialOptions: Option[] = [
                    {
                        label: placeholder || _x('Select...', 'admin-text', 'site-reviews'),
                        value: '',
                    },
                    ...response.map((item) => ({
                        label: item.title,
                        value: String(item.id),
                    })),
                ];
                setOptions(endpoint, initialOptions);
            })
            .finally(() => {
                setIsLoading(false);
            });
    }, []);

    return (
        <>
        {(!hideIfEmpty || options.length > 1) && (
            <Animate type={isLoading ? 'loading' : undefined}>
                {({ className }) => (
                    <BaseControl __nextHasNoMarginBottom>
                        <SelectControl
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                            className={className}
                            disabled={isLoading}
                            options={options}
                            {...controlProps}
                        />
                    </BaseControl>
                )}
            </Animate>
        )}
        </>
    );
};

export default AjaxSelectControl;
