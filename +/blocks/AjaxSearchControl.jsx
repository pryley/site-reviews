import apiFetch from '@wordpress/api-fetch';
import storeName from './Store';
import { _x } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import {
    __experimentalHStack as HStack,
    __experimentalText as Text,
    Animate,
    BaseControl,
    ComboboxControl,
    useBaseControlProps,
} from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useRef, useState } from '@wordpress/element';

/**
 * <AjaxSearchControl
 *     endpoint="/site-reviews/v1/shortcode/site_review?option=type"
 *     onChange={(type) => setAttributes({ type })}
 *     value={attributes.type}
 * />
 * 
 * @version 1.0
 */
const AjaxSearchControl = ({ endpoint, onChange, placeholder, value, ...props }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [search, setSearch] = useState('');
    const hasFetchedData = useRef(false);
    const options = useSelect(select => select(storeName).getOptions(endpoint), []);
    const { baseControlProps, controlProps } = useBaseControlProps(props);
    const { options: _, ...extraProps } = controlProps;
    const { setOptions } = useDispatch(storeName);

    const req = () => ({
        path: addQueryArgs(endpoint, {
            include: value,
            search,
        }),
    });

    const debouncedSearch = useDebounce(setSearch, 500);

    const transformItem = (item) => ({
        label: `${item.id}: ${item.title}`,
        title: item.title,
        value: String(item.id),
    });

    const initOptions = async () => {
        if (hasFetchedData.current) return
        if (options.length || !value) {
            hasFetchedData.current = true; // Mark that we've fetched the data
            return
        }
        setIsLoading(true)
        apiFetch(req()).then(response => {
            hasFetchedData.current = true; // Mark that we've fetched the data
            setOptions(endpoint, response.map(transformItem))
        }).finally(() => {
            setIsLoading(false)
        })
    };

    const performSearch = async () => {
        if (search.length < 2) return;
        setIsSearching(true)
        apiFetch(req()).then(response => {
            setOptions(endpoint, response.map(transformItem))
        }).finally(() => {
            setIsSearching(false)
        })
    };

    const computeMatch = (title) => {
        const matchText = search.toLocaleLowerCase();
        if (matchText.length === 0) {
            return null;
        }
        const indexOfMatch = title.toLocaleLowerCase().indexOf(matchText);
        return {
            afterMatch: title.substring(indexOfMatch + matchText.length),
            beforeMatch: title.substring(0, indexOfMatch),
            match: title.substring(indexOfMatch, indexOfMatch + matchText.length),
        }
    };

    const renderItem = ({ item }) => {
        const { title, value } = item;
        const matchText = computeMatch(title);
        return (
            <HStack>
                { matchText ? (
                    <Text color="inherit" numberOfLines={1} aria-label={ title }>
                        { matchText.beforeMatch }
                        <strong className="components-form-token-field__suggestion-match">
                            { matchText.match }
                        </strong>
                        { matchText.afterMatch }
                    </Text>
                ) : (
                    <Text color="inherit">{ title }</Text>
                ) }
                <Text color="inherit" size="small" style={{ flexShrink: 0, opacity: '0.5' }}>{ value }</Text>
            </HStack>
        )
    };

    // Run only on mount
    useEffect(() => { initOptions() }, [])

    // Fetch options whenever search changes
    useEffect(() => { performSearch() }, [search])

    return (
        <BaseControl __nextHasNoMarginBottom {...baseControlProps}>
            <Animate type={ (isLoading || isSearching) && 'loading' }>
                { ({ className }) => (
                    <ComboboxControl
                        __experimentalRenderItem={ renderItem }
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                        allowReset
                        className={ className }
                        expandOnFocus={ false }
                        options={ options }
                        onChange={ onChange }
                        onFilterValueChange={ debouncedSearch }
                        placeholder={ placeholder || _x('Search...', 'admin-text', 'site-reviews') }
                        value={ value }
                        { ...extraProps }
                    />
                ) }
            </Animate>
        </BaseControl>
    )
};

export default AjaxSearchControl;
