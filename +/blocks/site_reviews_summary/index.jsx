import edit from './edit';
import metadata from './block.json';
import { addFilter } from '@wordpress/hooks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-summary.svg';
import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

registerBlockType(metadata.name, {
    edit,
    icon: <Icon width={24} height={24} />,
});

addFilter('blocks.getBlockDefaultClassName', metadata.name, (className, blockName) => {
    return blockName === metadata.name ? 'wp-block-site-reviews-summary' : className;
});
