import { useEffect, useState } from 'react';
import { loggedFetch } from '@divi/rest';

interface Item {
    id: string | number;
    title: string;
}

interface TransformedItem {
    label: string;
    value: string;
}

export const useCheckboxesField = (shortcode: string, option: string) => {
    const [options, setOptions] = useState<TransformedItem[]>([]);

    const transformItem = (item: Item): TransformedItem => ({
        label: item.title,
        value: String(item.id),
    });

    useEffect(() => {
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
                    console.error('Failed to fetch hide options:', error);
                }
                setOptions([]);
            }
        };
        fetchOptions();
    }, []);

    return { options };
};
