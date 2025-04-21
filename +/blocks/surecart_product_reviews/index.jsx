import './style.scss';
import edit from './edit';
import metadata from './block.json';
import save from './save';
import { registerBlockType } from '@wordpress/blocks';

registerBlockType(metadata.name, { edit, save });
