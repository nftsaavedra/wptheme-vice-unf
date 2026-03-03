import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit.js';
import metadata from './block.json';
import './style.scss';

// Registramos el bloque usando los metadatos de block.json y el componente de edición.
registerBlockType(metadata.name, {
	edit: Edit,
	save: () => null, // Es un bloque dinámico, el guardado lo maneja render.php
});
