import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { BaseControl, ToggleControl, Spinner } from '@wordpress/components';
import { ControlProps, Item, Option } from './types';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

const AjaxToggleGroupControl = (props: ControlProps) => {
    const {
        endpoint,
        onChange,
        storeName = DEFAULT_STORE_NAME,
        value,
        ...controlProps
    } = props;
    const { checked: _, label: __, ...extraProps } = controlProps;

    const [isLoading, setIsLoading] = useState<boolean>(false);

    const registeredStoreName = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).getOptions(endpoint),
        []
    );
    const { setOptions } = useDispatch(registeredStoreName);

    const handleIsChecked = (optionValue: string, isChecked: boolean) => {
        const newValue = isChecked
            ? [...value, optionValue]
            : value.filter((v) => v !== optionValue);
        onChange(newValue);
    };

    useEffect(() => {
        if (options.length) return;
        setIsLoading(true);
        apiFetch<Item[]>({ path: endpoint })
            .then((response) => {
                const initialOptions: Option[] = response.map((item) => ({
                    label: item.title,
                    value: String(item.id),
                }));
                setOptions(endpoint, initialOptions);
            })
            .finally(() => {
                setIsLoading(false);
            });
    }, []);

    return (
        <BaseControl __nextHasNoMarginBottom>
            {!isLoading &&
                options.map((option) => (
                    <ToggleControl
                        __nextHasNoMarginBottom
                        key={option.value}
                        label={option.label}
                        checked={value.includes(option.value)}
                        onChange={(isChecked: boolean) => handleIsChecked(option.value, isChecked)}
                        {...extraProps}
                    />
                ))}
            {isLoading && <Spinner />}
        </BaseControl>
    );
};

export default AjaxToggleGroupControl;
