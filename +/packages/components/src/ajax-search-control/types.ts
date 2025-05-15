export interface ControlProps {
    endpoint: string;
    onChange: (value: string | undefined) => void;
    placeholder?: string;
    storeName?: string;
    value: string;
    label?: string;
    help?: string;
    [key: string]: any;
}

export interface Item {
    id: string | number;
    title: string;
}

export interface TransformedItem {
    label: string;
    title: string;
    value: string;
}

export interface SuggestionMatch {
    afterMatch: string;
    beforeMatch: string;
    match: string;
}
