import { getCSSValueFromRawStyle } from '@wordpress/style-engine';

interface OptionItem {
    id: string | number;
    title: string;
}

export interface Option {
    label: string;
    value: string;
}

/**
 * Transforms an endpoint item into a control option. The single source
 * of truth for the shape stored under the "options" key in the store
 * (used by AjaxComboboxControl, AjaxToggleGroupControl, and
 * prefetchOptions).
 */
export const toOption = (item: OptionItem): Option => ({
    label: item.title || String(item.id),
    value: String(item.id),
});

/**
 * Builds the CSS value for a ColorControl attribute pair. A theme preset
 * variable is undefined once the theme that provided it is no longer active,
 * and the declaration would then be invalid at computed-value time, leaving
 * the element it colours transparent. The colour the preset was picked as is
 * stored alongside the slug, so it is preferred; fallback covers markup that
 * carries a slug and nothing else.
 *
 * Mirrors Block::resolveColor() on the server.
 */
export const resolveColor = (preset: string, custom: string, fallback: string = 'currentColor'): string => {
    if (!preset) {
        return custom || '';
    }
    const value = getCSSValueFromRawStyle(`var:preset|color|${preset}`);
    return value.replace(/^var\((.+)\)$/, `var($1, ${custom || fallback})`);
};
