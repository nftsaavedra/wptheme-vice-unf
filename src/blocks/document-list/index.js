import { registerBlockType } from "@wordpress/blocks";
import Edit from "./edit.js";
import metadata from "./block.json";
import './style.scss';


/**
 * Registra el bloque con WordPress.
 */
registerBlockType(metadata.name, {
  edit: Edit,
  save: () => null, // Bloque dinámico renderizado en PHP
});
