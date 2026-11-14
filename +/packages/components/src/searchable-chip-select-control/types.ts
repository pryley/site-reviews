import type { ReactNode } from 'react';
import type { Item } from '../searchable-results/types';

export interface ControlProps {
    chipLabel?: (item: Item) => string;
    description?: ReactNode;
    disabled?: boolean;
    emptyContent?: ReactNode; // null hides "No results found."
    hideLabelFromVision?: boolean;
    items: Item[];
    label?: string;
    onInputValueChange?: (value: string) => void;
    onValueChange: (value: Item[]) => void;
    searchPlaceholder?: string;
    statusContent?: ReactNode;
    value: Item[];
}
