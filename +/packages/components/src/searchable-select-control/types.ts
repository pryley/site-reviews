import type { ReactNode } from 'react';
import type { Item } from '../searchable-results/types';

export interface ControlProps {
    clearButtonLabel?: string;
    description?: ReactNode;
    disabled?: boolean;
    emptyContent?: ReactNode; // null hides "No results found."
    items: Item[];
    label?: string;
    onInputValueChange?: (value: string) => void;
    onValueChange: (value: Item | null) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    showClearButton?: boolean;
    statusContent?: ReactNode;
    value: Item | null;
}
