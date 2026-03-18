import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { 
	InnerBlocks, 
	useBlockProps, 
	useInnerBlocksProps, 
	InspectorControls, 
	BlockControls, 
	AlignmentControl, 
	RichText 
} from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { columns, sectionTitle, sectionSubtitle, textAlign } = attributes;

	const TEMPLATE = [
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 1', 'viceunf' ), icon: 'fa-solid fa-rocket' } ],
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 2', 'viceunf' ), icon: 'fa-solid fa-lightbulb' } ],
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 3', 'viceunf' ), icon: 'fa-solid fa-graduation-cap' } ],
	];

	const blockProps = useBlockProps( {
		className: 'viceunf-benefits-grid-editor',
		style: { '--viceunf-grid-cols': columns }
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'viceunf-benefits-grid__grid' },
		{
			allowedBlocks: [ 'viceunf/benefit-card' ],
			template: TEMPLATE,
			orientation: "horizontal"
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración de Sección', 'viceunf' ) }>
					<RangeControl
						label={ __( 'Columnas', 'viceunf' ) }
						value={ columns }
						onChange={ ( val ) => setAttributes( { columns: val } ) }
						min={ 2 }
						max={ 4 }
					/>
				</PanelBody>
			</InspectorControls>

			<BlockControls>
				<AlignmentControl
					value={ textAlign }
					onChange={ ( nextAlign ) => setAttributes( { textAlign: nextAlign } ) }
				/>
			</BlockControls>

			<div { ...blockProps }>
				<div style={ { textAlign: textAlign, marginBottom: '3.2rem', color: 'inherit' } }>
					<RichText
						tagName="h2"
						className="viceunf-benefits-grid__title"
						style={ { marginBottom: '1rem' } }
						value={ sectionTitle }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
						placeholder={ __( 'Escribe el Título de sección...', 'viceunf' ) }
					/>
					<RichText
						tagName="p"
						className="viceunf-benefits-grid__subtitle"
						value={ sectionSubtitle }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
						onChange={ ( val ) => setAttributes( { sectionSubtitle: val } ) }
						placeholder={ __( 'Escribe un Subtítulo (opcional)...', 'viceunf' ) }
					/>
				</div>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
