import './style.scss';
import edit from './edit';
import metadata from './block.json';
import { registerBlockType } from '@wordpress/blocks';

registerBlockType(metadata.name, { edit });
