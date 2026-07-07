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
