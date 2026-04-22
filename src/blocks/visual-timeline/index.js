import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, useInnerBlocksProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'vpinunf/timeline-step', { stepNumber: 1, title: __( 'Primer paso', 'vpinunf' ), description: __( 'Descripción del primer paso.', 'vpinunf' ) } ],
	[ 'vpinunf/timeline-step', { stepNumber: 2, title: __( 'Segundo paso', 'vpinunf' ), description: __( 'Descripción del segundo paso.', 'vpinunf' ), accentColor: '#0e1422' } ],
	[ 'vpinunf/timeline-step', { stepNumber: 3, title: __( 'Tercer paso', 'vpinunf' ), description: __( 'Descripción del tercer paso.', 'vpinunf' ) } ],
];

function Edit( { attributes, setAttributes } ) {
	const { lineColor, sectionTitle } = attributes;
	const blockProps = useBlockProps( { className: 'vpinunf-visual-timeline-editor' } );	const { children, ...innerBlocksProps } = useInnerBlocksProps(
		{ className: 'vpinunf-visual-timeline__track' },
		{
			allowedBlocks: [ 'vpinunf/timeline-step' ],
			template: TEMPLATE,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'vpinunf' ) }>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>
						{ __( 'Color de la línea vertical', 'vpinunf' ) }
					</p>
					<ColorPalette
						value={ lineColor }
						onChange={ ( val ) => setAttributes( { lineColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--vpinunf-timeline-line': lineColor } }>
				<RichText
					tagName="h2"
					value={ sectionTitle }
					onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					placeholder={ __( 'Título de sección (opcional)...', 'vpinunf' ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					style={ { textAlign: 'center', marginBottom: '5rem' } }
				/>
				<div { ...innerBlocksProps }>
					<div className="vpinunf-visual-timeline__line" aria-hidden="true" style={ { backgroundColor: lineColor } }></div>
					{ children }
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
