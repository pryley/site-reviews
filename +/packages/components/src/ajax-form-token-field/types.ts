export interface ControlProps {
    endpoint: string;
    help?: string;
    label?: string;
    onChange: (value: string[]) => void;
    placeholder?: string;
    prefetch?: boolean;
    storeName?: string;
    value: string[];
}

export interface Item {
    id: string;
    title: string;
}

export interface TransformedItem {
    id: string;
    title: string;
    value: string;
}

export interface SuggestionMatch {
    afterMatch: string;
    beforeMatch: string;
    match: string;
}
