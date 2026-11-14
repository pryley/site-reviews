import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _x } from '@wordpress/i18n';
import { ControlProps, Item, Option } from './types';
import { toOption } from '../utils';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useMemo, useState } from '@wordpress/element';
import SearchableSelectControl, { LoadingIndicator } from '../searchable-select-control';
import type { Item as Choice } from '../searchable-results/types';

const toChoice = (option: Option): Choice => ({
    description: option.value,
    label: option.label,
    value: option.value,
});

const AjaxComboboxControl = (props: ControlProps) => {
    const {
        endpoint,
        fallback = null,
        help,
        hideIfEmpty = false,
        label,
        onChange,
        placeholder,
        storeName = DEFAULT_STORE_NAME,
        value,
    } = props;

    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [search, setSearch] = useState<string>('');

    const registeredStoreName: string = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).get('options', endpoint) || [],
        [endpoint, registeredStoreName]
    );
    const { set: setStoreValues } = useDispatch(registeredStoreName);

    const initOptions = async () => {
        if (options.length) return;
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>({ path: endpoint });
            setStoreValues('options', endpoint, response.map(toOption));
        } finally {
            setIsLoading(false);
        }
    };

    const choices = useMemo(() => {
        const text = search.toLocaleLowerCase();
        return options
            .filter((option) => !text || `${option.value}: ${option.label}`.toLocaleLowerCase().includes(text))
            .map(toChoice);
    }, [options, search]);

    const selected = useMemo(() => {
        const option = options.find((candidate) => candidate.value === value);
        return option ? toChoice(option) : null;
    }, [options, value]);

    useEffect(() => { initOptions() }, [endpoint]);

    if (0 === options.length && (fallback || hideIfEmpty)) {
        return isLoading ? null : fallback;
    }

    return (
        <SearchableSelectControl
            description={help}
            emptyContent={isLoading ? null : undefined}
            items={choices}
            label={label}
            onInputValueChange={setSearch}
            onValueChange={(choice) => onChange?.(choice ? choice.value : null)}
            placeholder={placeholder || _x('Select...', 'admin-text', 'site-reviews')}
            showClearButton
            statusContent={isLoading ? <LoadingIndicator /> : undefined}
            value={selected}
        />
    );
};

export default AjaxComboboxControl;
