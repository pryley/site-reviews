import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { _x } from '@wordpress/i18n';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    Animate,
    BaseControl,
    ComboboxControl,
    useBaseControlProps,
} from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { ControlProps, Item, Match, Option } from './types';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useRef, useState } from '@wordpress/element';

const AjaxSearchControl = (props: ControlProps) => {
    const {
        endpoint,
        onChange,
        options: _, // discard
        placeholder,
        storeName = DEFAULT_STORE_NAME,
        value,
        ...additionalProps
    } = props;
    const { baseControlProps, controlProps } = useBaseControlProps(additionalProps);

    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [isSearching, setIsSearching] = useState<boolean>(false);
    const [search, setSearch] = useState<string>('');
    const hasFetchedData = useRef<boolean>(false);

    const registeredStoreName = createStore(storeName);
    const options = useSelect<Option[]>(
        (select) => select(registeredStoreName).getOptions(endpoint),
        []
    );
    const { setOptions } = useDispatch(registeredStoreName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value,
            search,
        }),
    });

    const debouncedSearch = useDebounce(setSearch, 250);

    const transformItem = (item: Item): Option => ({
        label: `${item.id}: ${item.title}`,
        title: item.title,
        value: String(item.id),
    });

    const initOptions = async () => {
        if (hasFetchedData.current) return;
        if (options.length || !value) {
            hasFetchedData.current = true; // Mark that we've fetched the data
            return;
        }
        setIsLoading(true);
        try {
            const response = await apiFetch<Item[]>(req());
            hasFetchedData.current = true; // Mark that we've fetched the data
            setOptions(endpoint, response.map(transformItem));
        } finally {
            setIsLoading(false);
        }
    };

    const performSearch = async () => {
        if (search.length < 2) return;
        setIsSearching(true);
        try {
            const response = await apiFetch<Item[]>(req());
            setOptions(endpoint, response.map(transformItem));
        } finally {
            setIsSearching(false);
        }
    };

    const computeMatch = (title: string): Match | null => {
        const matchText = search.toLocaleLowerCase();
        if (matchText.length === 0) {
            return null;
        }
        const indexOfMatch = title.toLocaleLowerCase().indexOf(matchText);
        return {
            afterMatch: title.substring(indexOfMatch + matchText.length),
            beforeMatch: title.substring(0, indexOfMatch),
            match: title.substring(indexOfMatch, indexOfMatch + matchText.length),
        };
    };

    const renderItem = ({ item }: { item: Option }) => {
        const { title, value } = item;
        const matchText = computeMatch(title);
        return (
            <HStack>
                {matchText ? (
                    <Text color="inherit" numberOfLines={1} aria-label={title}>
                        {matchText.beforeMatch}
                        <strong className="components-form-token-field__suggestion-match">
                            {matchText.match}
                        </strong>
                        {matchText.afterMatch}
                    </Text>
                ) : (
                    <Text color="inherit">{title}</Text>
                )}
                <Text color="inherit" size="small" style={{ flexShrink: 0, opacity: '0.5' }}>
                    {value}
                </Text>
            </HStack>
        );
    };

    // Run only on mount
    useEffect(() => { initOptions() }, [])

    // Fetch options whenever search changes
    useEffect(() => { performSearch() }, [search])

    return (
        <BaseControl __nextHasNoMarginBottom {...baseControlProps}>
            <Animate type={(isLoading || isSearching) ? 'loading' : undefined}>
                {({ className }) => (
                    <ComboboxControl
                        __experimentalRenderItem={renderItem}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        allowReset
                        className={className}
                        expandOnFocus={false}
                        options={options}
                        onChange={onChange}
                        onFilterValueChange={debouncedSearch}
                        placeholder={
                            placeholder || _x('Search...', 'admin-text', 'site-reviews')
                        }
                        value={value}
                        {...controlProps}
                    />
                )}
            </Animate>
        </BaseControl>
    );
};

export default AjaxSearchControl;
