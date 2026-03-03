import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ColorPicker } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { icon, title, description, gradientStart, gradientEnd } = attributes;

	const blockProps = useBlockProps( {
		className: 'viceunf-benefit-card',
		style: {
			background: `linear-gradient(135deg, ${ gradientStart } 0%, ${ gradientEnd } 100%)`,
		},
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contenido de la Tarjeta', 'viceunf' ) }>
					<TextControl
						label={ __( 'Clase de ícono FontAwesome (ej: fa-solid fa-rocket)', 'viceunf' ) }
						help={ __( 'Consulta fontawesome.com para las clases disponibles.', 'viceunf' ) }
						value={ icon }
						onChange={ ( val ) => setAttributes( { icon: val } ) }
					/>
					<TextControl
						label={ __( 'Título', 'viceunf' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextControl
						label={ __( 'Descripción', 'viceunf' ) }
						value={ description }
						onChange={ ( val ) => setAttributes( { description: val } ) }
						help={ __( 'Descripción corta del beneficio.', 'viceunf' ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Color de Degradado', 'viceunf' ) } initialOpen={ false }>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>{ __( 'Color inicio', 'viceunf' ) }</p>
					<ColorPicker
						color={ gradientStart }
						onChange={ ( val ) => setAttributes( { gradientStart: val } ) }
						enableAlpha={ false }
					/>
					<p style={ { fontSize: '12px', marginBottom: '8px', marginTop: '16px' } }>{ __( 'Color fin', 'viceunf' ) }</p>
					<ColorPicker
						color={ gradientEnd }
						onChange={ ( val ) => setAttributes( { gradientEnd: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="viceunf-benefit-card__icon-wrap">
					<i className={ icon } aria-hidden="true"></i>
				</div>
				<h3 className="viceunf-benefit-card__title">{ title || __( 'Título del beneficio', 'viceunf' ) }</h3>
				<p className="viceunf-benefit-card__desc">{ description || __( 'Descripción del beneficio.', 'viceunf' ) }</p>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
