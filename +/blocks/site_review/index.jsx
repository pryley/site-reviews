import edit from './edit';
import metadata from './block.json';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-review.svg';

registerBlockType(metadata, {
    edit,
    icon: <Icon width={24} height={24} />,
});
