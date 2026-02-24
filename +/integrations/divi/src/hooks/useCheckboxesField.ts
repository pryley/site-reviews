import { useEffect, useState } from 'react';
import { loggedFetch } from '@divi/rest';
import { type Item, type TransformedItem } from '@site-reviews-divi/shared/types';

export const useCheckboxesField = (shortcode: string, option: string) => {
    const [options, setOptions] = useState<TransformedItem[]>([]);

    const transformItem = (item: Item): TransformedItem => ({
        label: item.title,
        value: String(item.id),
    });

    const fetchOptions = async () => {
        try {
            const response = await loggedFetch({
                data: { option },
                method: 'GET',
                restRoute: `/site-reviews/v1/shortcode/${shortcode}`,
            });
            setOptions(response.map(transformItem));
        } catch (error) {
            if ('AbortError' !== (error as Error).name) {
                console.error('Failed to fetch options:', error);
            }
            setOptions([]);
        }
    };

    useEffect(() => { fetchOptions() }, [])

    return { options };
};
