export interface ControlProps {
    help?: string;
    label?: string;
    onChange: (value: boolean) => void;
    value: boolean;
    [key: string]: any;
}
