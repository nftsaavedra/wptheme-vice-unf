import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ColorPicker } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'viceunf/timeline-step', { stepNumber: 1, title: __( 'Primer paso', 'viceunf' ), description: __( 'Descripción del primer paso.', 'viceunf' ) } ],
	[ 'viceunf/timeline-step', { stepNumber: 2, title: __( 'Segundo paso', 'viceunf' ), description: __( 'Descripción del segundo paso.', 'viceunf' ), accentColor: '#0e1422' } ],
	[ 'viceunf/timeline-step', { stepNumber: 3, title: __( 'Tercer paso', 'viceunf' ), description: __( 'Descripción del tercer paso.', 'viceunf' ) } ],
];

function Edit( { attributes, setAttributes } ) {
	const { lineColor, sectionTitle } = attributes;
	const blockProps = useBlockProps( { className: 'viceunf-visual-timeline-editor' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
					<TextControl
						label={ __( 'Título de sección (opcional)', 'viceunf' ) }
						value={ sectionTitle }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					/>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>
						{ __( 'Color de la línea vertical', 'viceunf' ) }
					</p>
					<ColorPicker
						color={ lineColor }
						onChange={ ( val ) => setAttributes( { lineColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--viceunf-timeline-line': lineColor } }>
				{ sectionTitle && (
					<h2 style={ { textAlign: 'center', marginBottom: '5rem' } }>{ sectionTitle }</h2>
				) }
				<div className="viceunf-visual-timeline__track">
					<div className="viceunf-visual-timeline__line" aria-hidden="true"></div>
					<InnerBlocks
						allowedBlocks={ [ 'viceunf/timeline-step' ] }
						template={ TEMPLATE }
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
