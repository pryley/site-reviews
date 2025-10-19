import { useCallback, useEffect, useState } from 'react';
import { loggedFetch } from '@divi/rest';
import { type FieldLibrary } from '@divi/types';

interface Item {
    id: string | number;
    title: string;
}

export const useFormTokenField = (shortcode: string, option: string, value: string[]) => {
    // @ts-expect-error
    const [options, setOptions] = useState<FieldLibrary.TagInput.Options>({});
    const [search, setSearch] = useState<string>('');

    const data = () => ({
        data: {
            include: value.join(','),
            option,
            search,
        },
        method: 'GET',
        restRoute: `/site-reviews/v1/shortcode/${shortcode}`,
    });

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
            console.error('Failed to fetch options:', error);
            setOptions({});
        }
    };

    const onDropdownClose = useCallback(() => {
        setSearch('');
    }, []);

    const onSearchChange = useCallback((inputValue: string) => {
        setSearch(inputValue);
    }, []);

    useEffect(() => { fetchOptions() }, [search])

    return { onSearchChange, onDropdownClose, options };
};
