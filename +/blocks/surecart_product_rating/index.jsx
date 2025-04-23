import './style.scss';
import edit from './edit';
import metadata from './block.json';
import { Icon, starEmpty } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';

registerBlockType(metadata.name, {
    edit,
    icon: <Icon icon={starEmpty} />,
});
