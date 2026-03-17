import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit.js';
import save from './save.js';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: Edit,
    save: save,
});
