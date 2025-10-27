import { useCallback, useEffect, useState } from 'react';
import { loggedFetch } from '@divi/rest';
import { type FieldLibrary } from '@divi/types';
import { type Item } from '../shared/types';
import { pickBy } from 'lodash';

export const useFormTokenField = (shortcode: string, option: string, value: string[]) => {
    // @ts-expect-error
    const [options, setOptions] = useState<FieldLibrary.TagInput.Options>({});
    const [search, setSearch] = useState<string>('');

    const data = () => {
        const data = {
            ...Object.fromEntries(new URLSearchParams(`option=${option}`)),
            include: value.join(','),
            search,
        };
        return {
            data: pickBy(data, value => '' !== value),
            method: 'GET',
            restRoute: `/site-reviews/v1/shortcode/${shortcode}`,
        }
    };

    const transformResponse = (items: Item[]) => {
        return Object.fromEntries(items.map((item: any) => [
            item.id.toString(),
            { label: item.title },
        ]))
    };

    const fetchOptions = async () => {
        try {
            const response = await loggedFetch(data());
            setOptions(transformResponse(response));
        } catch (error) {
            if ('AbortError' !== (error as Error).name) {
                console.error('Failed to fetch options:', error);
            }
            setOptions({});
        }
    };

    const onDropdownClose = useCallback(() => {
        setSearch('');
    }, []);

    const onSearchChange = useCallback((inputValue: string) => {
        setSearch(inputValue);
    }, []);

    useEffect(() => { fetchOptions() }, [search, option])

    return { onSearchChange, onDropdownClose, options };
};
