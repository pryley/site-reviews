export interface ControlProps {
  endpoint: string;
  hideIfEmpty?: boolean;
  placeholder?: string;
  storeName?: string,
  value?: string;
  onChange?: (value: string | null) => void; // null when the selection is cleared
  [key: string]: any;
}

export interface Item {
  id: number | string;
  title: string;
}

export interface Option {
  label: string;
  value: string;
}
