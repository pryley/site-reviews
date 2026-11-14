import { getCSSValueFromRawStyle } from '@wordpress/style-engine';

interface OptionItem {
    id: string | number;
    title: string;
}

export interface Option {
    label: string;
    value: string;
}

export const toOption = (item: OptionItem): Option => ({
    label: item.title || String(item.id),
    value: String(item.id),
});

// two or more characters or any number (Post IDs can be a single digit)
export const isSearchable = (search: string): boolean => search.length > 1 || /^\d+$/.test(search);

/**
 * Builds the CSS value for a ColorControl attribute pair.
 * Mirrors Block::resolveColor() on the server.
 */
export const resolveColor = (preset: string, custom: string, fallback: string = 'currentColor'): string => {
    if (!preset) {
        return custom || '';
    }
    const value = getCSSValueFromRawStyle(`var:preset|color|${preset}`);
    return value.replace(/^var\((.+)\)$/, `var($1, ${custom || fallback})`);
};
