import React, {
    createElement,
    type ReactElement,
    type ReactNode,
} from 'react';

import { type HeadingProps } from './types';


/**
 * Renders a heading element based on the specified heading level.
 *
 * @since ??
 *
 * @param {object} props The component props.
 * @param {string} props.headingLevel The level of the heading element (default: 'h2').
 * @param {ReactNode} props.children The content to be rendered inside the heading element.
 * @returns {ReactElement} The rendered heading element.
 */
const Heading = ({
    headingLevel = 'h2',
    children,
}: HeadingProps): ReactElement => {
    switch (headingLevel) {
        case 'h1':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
            return createElement(headingLevel, { className: 'entry-title' }, children);

        default:
            return <h2 className="entry-title">{children}</h2>;
    }
};

export {
    Heading,
};
