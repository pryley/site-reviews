export interface ControlProps {
    endpoint: string;
    hideIfEmpty?: boolean;
    placeholder?: string;
    storeName?: string;
    onChange: (value: string) => void;
    value: string;
    label?: string;
    help?: string;
    [key: string]: any;
}

export interface Item {
    id: string | number;
    title: string;
}

export interface Option {
    label: string;
    value: string;
}

