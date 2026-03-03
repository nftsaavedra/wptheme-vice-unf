import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	ColorPicker,
} from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { title, subtitle, buttonText, buttonUrl, buttonColor, bgColor, showLogos } = attributes;

	const blockProps = useBlockProps( {
		className: 'viceunf-footer-cta-editor',
		style: { backgroundColor: bgColor, padding: '6rem 3rem', textAlign: 'center', borderRadius: '4px' },
	} );

	const btnStyle = buttonColor
		? { backgroundColor: buttonColor, borderColor: buttonColor, color: '#fff' }
		: {};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contenido', 'viceunf' ) }>
					<TextControl
						label={ __( 'Título', 'viceunf' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextControl
						label={ __( 'Subtítulo', 'viceunf' ) }
						value={ subtitle }
						onChange={ ( val ) => setAttributes( { subtitle: val } ) }
					/>
					<TextControl
						label={ __( 'Texto del botón', 'viceunf' ) }
						value={ buttonText }
						onChange={ ( val ) => setAttributes( { buttonText: val } ) }
					/>
					<TextControl
						label={ __( 'URL del botón', 'viceunf' ) }
						value={ buttonUrl }
						onChange={ ( val ) => setAttributes( { buttonUrl: val } ) }
						type="url"
					/>
					<ToggleControl
						label={ __( 'Mostrar logos institucionales', 'viceunf' ) }
						help={ __( 'Muestra el logo del sitio (Custom Logo) debajo del botón.', 'viceunf' ) }
						checked={ showLogos }
						onChange={ ( val ) => setAttributes( { showLogos: val } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Colores', 'viceunf' ) } initialOpen={ false }>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>{ __( 'Color de fondo de la sección', 'viceunf' ) }</p>
					<ColorPicker
						color={ bgColor }
						onChange={ ( val ) => setAttributes( { bgColor: val } ) }
						enableAlpha={ false }
					/>
					<p style={ { fontSize: '12px', marginTop: '16px', marginBottom: '8px' } }>{ __( 'Color del botón (vacío = color primario del tema)', 'viceunf' ) }</p>
					<ColorPicker
						color={ buttonColor }
						onChange={ ( val ) => setAttributes( { buttonColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div style={ { maxWidth: '760px', margin: '0 auto' } }>
					{ title && (
						<h2 style={ { color: '#fff', fontWeight: 800, marginBottom: '1.2rem', fontSize: '3.6rem', margin: '0 0 1.2rem' } }>
							{ title }
						</h2>
					) }
					{ subtitle && (
						<p style={ { color: 'rgba(255,255,255,0.75)', fontSize: '1.8rem', marginBottom: '3.2rem' } }>
							{ subtitle }
						</p>
					) }
					{ buttonText && (
						<span
							className="dt-btn dt-btn-primary"
							style={ { ...btnStyle, padding: '1.8rem 5rem', fontSize: '1.8rem', display: 'inline-block', cursor: 'pointer' } }
						>
							{ buttonText }
						</span>
					) }
					{ showLogos && (
						<p style={ { color: 'rgba(255,255,255,0.5)', marginTop: '3.2rem', fontSize: '1.2rem' } }>
							{ __( '[Logos institucionales aquí]', 'viceunf' ) }
						</p>
					) }
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
