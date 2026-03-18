import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, useInnerBlocksProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'viceunf/timeline-step', { stepNumber: 1, title: __( 'Primer paso', 'viceunf' ), description: __( 'Descripción del primer paso.', 'viceunf' ) } ],
	[ 'viceunf/timeline-step', { stepNumber: 2, title: __( 'Segundo paso', 'viceunf' ), description: __( 'Descripción del segundo paso.', 'viceunf' ), accentColor: '#0e1422' } ],
	[ 'viceunf/timeline-step', { stepNumber: 3, title: __( 'Tercer paso', 'viceunf' ), description: __( 'Descripción del tercer paso.', 'viceunf' ) } ],
];

function Edit( { attributes, setAttributes } ) {
	const { lineColor, sectionTitle } = attributes;
	const blockProps = useBlockProps( { className: 'viceunf-visual-timeline-editor' } );	const { children, ...innerBlocksProps } = useInnerBlocksProps(
		{ className: 'viceunf-visual-timeline__track' },
		{
			allowedBlocks: [ 'viceunf/timeline-step' ],
			template: TEMPLATE,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>
						{ __( 'Color de la línea vertical', 'viceunf' ) }
					</p>
					<ColorPalette
						value={ lineColor }
						onChange={ ( val ) => setAttributes( { lineColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--viceunf-timeline-line': lineColor } }>
				<RichText
					tagName="h2"
					value={ sectionTitle }
					onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					placeholder={ __( 'Título de sección (opcional)...', 'viceunf' ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					style={ { textAlign: 'center', marginBottom: '5rem' } }
				/>
				<div { ...innerBlocksProps }>
					<div className="viceunf-visual-timeline__line" aria-hidden="true" style={ { backgroundColor: lineColor } }></div>
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
