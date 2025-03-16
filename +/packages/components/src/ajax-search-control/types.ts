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

export interface Match {
    afterMatch: string;
    beforeMatch: string;
    match: string;
}

export interface Option {
    label: string;
    title: string;
    value: string;
}
