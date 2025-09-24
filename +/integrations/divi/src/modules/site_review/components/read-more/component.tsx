import React, { type ReactElement } from 'react';

import { __ } from '@wordpress/i18n';

import { type ReadMoreProps } from './types';

/**
 * Read more component for the Blog module.
 *
 * @param {ReadMoreProps} props Read more component props.
 *
 * @returns {ReactElement}
 */
const ReadMore = ({ permalink }: ReadMoreProps): ReactElement => (
    <a href={permalink} className="more-link">{__('read more', 'et_builder')}</a>
);

export {
    ReadMore,
};
