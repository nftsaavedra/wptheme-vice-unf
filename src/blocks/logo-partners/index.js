import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { sectionTitle, displayMode, grayscaleDefault, source } = attributes;
	const blockProps = useBlockProps( { className: 'viceunf-logo-partners-editor' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
					<TextControl
						label={ __( 'Título de sección', 'viceunf' ) }
						value={ sectionTitle }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					/>
					<SelectControl
						label={ __( 'Fuente de logos', 'viceunf' ) }
						value={ source }
						options={ [
							{ label: __( 'CPT Socio (automático)', 'viceunf' ), value: 'cpt' },
						] }
						onChange={ ( val ) => setAttributes( { source: val } ) }
						help={ __( 'Los logos se obtienen desde el CPT "Socio" del plugin viceunf-core.', 'viceunf' ) }
					/>
					<SelectControl
						label={ __( 'Modo de visualización', 'viceunf' ) }
						value={ displayMode }
						options={ [
							{ label: __( 'Estático', 'viceunf' ), value: 'static' },
						] }
						onChange={ ( val ) => setAttributes( { displayMode: val } ) }
					/>
					<ToggleControl
						label={ __( 'Logos en gris por defecto', 'viceunf' ) }
						help={ __( 'Los logos pasan a color al hacer hover.', 'viceunf' ) }
						checked={ grayscaleDefault }
						onChange={ ( val ) => setAttributes( { grayscaleDefault: val } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ sectionTitle && (
					<h2 style={ { textAlign: 'center', marginBottom: '3.2rem', color: '#0e1422' } }>
						{ sectionTitle }
					</h2>
				) }
				<div style={ { textAlign: 'center', padding: '4rem', border: '1px dashed #ccc', borderRadius: '8px', color: '#999' } }>
					<i className="fa-solid fa-handshake" style={ { fontSize: '3rem', marginBottom: '1.2rem', display: 'block' } }></i>
					<p style={ { margin: 0 } }>
						{ __( 'Los logos de socios (CPT "Socio") se renderizarán aquí en el frontend.', 'viceunf' ) }
					</p>
					<p style={ { margin: '0.4rem 0 0', fontSize: '1.2rem' } }>
						{ grayscaleDefault
							? __( 'Modo: gris por defecto → color al hover', 'viceunf' )
							: __( 'Modo: color siempre', 'viceunf' ) }
					</p>
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
