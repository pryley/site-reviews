import Edit from './edit';
import metadata from './block.json';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-form.svg';
import { store } from '../Store.jsx';

registerBlockType(metadata.name, {
    edit: Edit,
    icon: <Icon width={24} height={24} />,
});
