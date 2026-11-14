import type { ReactNode } from 'react';

export interface Item {
    description?: string;
    label: string;
    value: string;
}

export interface ResultsProps {
    activeIndex: number; // the highlighted item or -1
    emptyContent?: ReactNode; // null hides "No results found."
    id: string;
    isPicked: (item: Item) => boolean;
    items: Item[];
    label?: string;
    multiple?: boolean;
    onHover: (index: number) => void;
    onPick: (item: Item) => void;
    statusContent?: ReactNode;
}
