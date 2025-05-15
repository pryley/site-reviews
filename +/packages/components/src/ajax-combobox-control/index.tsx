import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    Animate,
    BaseControl,
    ComboboxControl,
} from '@wordpress/components';
import { ControlProps, Item, Option } from './types';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

const AjaxComboboxControl = (props: ControlProps) => {
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
        (select) => select(registeredStoreName).getOptions(endpoint) || [],
        [endpoint, registeredStoreName]
    );
    const { setOptions } = useDispatch(registeredStoreName);

    const renderItem = ({ item }: { item: Option }) => {
        const { label, value } = item;
        return (
            <HStack>
                <Text color="inherit">{label}</Text>
                <Text color="inherit" size="small" style={{ flexShrink: 0, opacity: 0.5 }}>
                    {value}
                </Text>
            </HStack>
        );
    };

    const transformItem = (item: Item): Option => ({
        label: item.title || String(item.id),
        value: String(item.id),
    });

    const initOptions = async () => {
        if (options.length) return;
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>({ path: endpoint });
            setOptions(endpoint, response.map(transformItem));
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
            <Animate type={ isLoading ? 'loading' : undefined }>
                {({ className }) => (
                    <ComboboxControl
                        __experimentalRenderItem={renderItem}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        allowReset
                        className={className}
                        expandOnFocus={false}
                        options={options}
                        placeholder={
                            placeholder || _x('Select...', 'admin-text', 'site-reviews')
                        }
                        {...controlProps}
                    />
                ) }
            </Animate>
            {help && (
                <Text variant="muted" size="small">
                    {help}
                </Text>
            )}
        </BaseControl>
    )
};

export default AjaxComboboxControl;
