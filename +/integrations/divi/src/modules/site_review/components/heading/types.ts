import { type ReactNode } from 'react';

export interface HeadingProps {
    headingLevel: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    children?: ReactNode;
}
