import edit from './edit';
import metadata from './block.json';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-form.svg';

registerBlockType(metadata.name, {
    edit,
    icon: <Icon width={24} height={24} />,
});
