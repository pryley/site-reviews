import './style.scss';
import edit from './edit';
import metadata from './block.json';
import { addFilter } from '@wordpress/hooks';
import { registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-reviews.svg';

registerBlockType(metadata.name, {
    edit,
    icon: <Icon width={24} height={24} />,
});

addFilter('blocks.getBlockDefaultClassName', metadata.name, (className, blockName) => {
    return blockName === metadata.name ? 'wp-block-site-reviews' : className;
});
