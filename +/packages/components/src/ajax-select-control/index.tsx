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
        help,
        hideIfEmpty = false,
        options: _, // discard
        placeholder,
        storeName = DEFAULT_STORE_NAME,
        ...controlProps
    } = props;

    const [isLoading, setIsLoading] = useState<boolean>(false);

    const registeredStoreName: string = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).get('options', endpoint) || [],
        [endpoint, registeredStoreName]
    );
    const { set } = useDispatch(registeredStoreName);

    const transformItem = (item: Item): Option => ({
        label: item.title || String(item.id),
        value: String(item.id),
    });

    const initOptions = async () => {
        if (options.length) return;
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>({ path: endpoint });
            set('options', endpoint, response.map(transformItem));
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => { initOptions() }, [endpoint]);

    if (0 === options.length && (fallback || hideIfEmpty)) {
        return isLoading ? null : fallback;
    }

    return (
        <BaseControl __nextHasNoMarginBottom>
            <Animate type={isLoading ? 'loading' : undefined}>
                {({ className }) => (
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
                )}
            </Animate>
            {help && (
                <Text variant="muted" size="small">
                    {help}
                </Text>
            )}
        </BaseControl>
    );
};

export default AjaxSelectControl;
