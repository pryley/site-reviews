export interface ControlProps {
    endpoint: string;
    onChange: (value: string[]) => void;
    storeName?: string;
    value: string[];
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
