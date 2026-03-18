import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl, RangeControl, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { stepNumber, title, description, icon, accentColor } = attributes;

	const blockProps = useBlockProps( { className: 'viceunf-timeline-step' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contenido del Paso', 'viceunf' ) }>
					<RangeControl
						label={ __( 'Número del paso', 'viceunf' ) }
						value={ stepNumber }
						onChange={ ( val ) => setAttributes( { stepNumber: val } ) }
						min={ 1 }
						max={ 20 }
					/>
					<TextControl
						label={ __( 'Clase de ícono FA (opcional)', 'viceunf' ) }
						value={ icon }
						onChange={ ( val ) => setAttributes( { icon: val } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Color de acento', 'viceunf' ) } initialOpen={ false }>
					<ColorPalette
						value={ accentColor }
						onChange={ ( val ) => setAttributes( { accentColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--viceunf-accent': accentColor } }>
				<div className="viceunf-timeline-step__bubble" style={ { backgroundColor: accentColor } }>
					{ icon ? (
						<i className={ icon.replace( /[^a-zA-Z0-9\s\-]/g, '' ) } aria-hidden="true"></i>
					) : (
						<span>{ stepNumber }</span>
					) }
				</div>
				<div className="viceunf-timeline-step__card" style={ { borderLeftColor: accentColor } }>
					<RichText
						tagName="h3"
						className="viceunf-timeline-step__title"
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
						placeholder={ __( 'Título del paso', 'viceunf' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
					<RichText
						tagName="p"
						className="viceunf-timeline-step__desc"
						value={ description }
						onChange={ ( val ) => setAttributes( { description: val } ) }
						placeholder={ __( 'Descripción del paso...', 'viceunf' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
