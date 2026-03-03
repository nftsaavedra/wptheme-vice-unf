import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, RangeControl, ColorPicker } from '@wordpress/components';
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
						label={ __( 'Título', 'viceunf' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextareaControl
						label={ __( 'Descripción', 'viceunf' ) }
						value={ description }
						onChange={ ( val ) => setAttributes( { description: val } ) }
					/>
					<TextControl
						label={ __( 'Clase de ícono FA (opcional)', 'viceunf' ) }
						value={ icon }
						onChange={ ( val ) => setAttributes( { icon: val } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Color de acento', 'viceunf' ) } initialOpen={ false }>
					<ColorPicker
						color={ accentColor }
						onChange={ ( val ) => setAttributes( { accentColor: val } ) }
						enableAlpha={ false }
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
					{ title && <h3 className="viceunf-timeline-step__title">{ title }</h3> }
					{ description && <p className="viceunf-timeline-step__desc">{ description }</p> }
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
