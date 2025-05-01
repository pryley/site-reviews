import './style.scss';
import edit from './edit';
import metadata from './block.json';
import { registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Icon } from '../../../assets/images/icons/gutenberg/icon-review.svg';

registerBlockType(metadata, {
    edit,
    icon: <Icon width={24} height={24} />,
});

wp.hooks.addFilter('blocks.getBlockDefaultClassName', metadata.name, (className, blockName) => {
    return blockName !== metadata.name ? className : 'wp-block-site-review';
});
