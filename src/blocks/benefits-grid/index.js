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
		[ 'vpinunf/benefit-card', { title: __( 'Beneficio 1', 'vpinunf' ), icon: 'fa-solid fa-rocket' } ],
		[ 'vpinunf/benefit-card', { title: __( 'Beneficio 2', 'vpinunf' ), icon: 'fa-solid fa-lightbulb' } ],
		[ 'vpinunf/benefit-card', { title: __( 'Beneficio 3', 'vpinunf' ), icon: 'fa-solid fa-graduation-cap' } ],
	];

	const blockProps = useBlockProps( {
		className: 'vpinunf-benefits-grid-editor',
		style: { '--vpinunf-grid-cols': columns }
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'vpinunf-benefits-grid__grid' },
		{
			allowedBlocks: [ 'vpinunf/benefit-card' ],
			template: TEMPLATE,
			orientation: "horizontal"
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración de Sección', 'vpinunf' ) }>
					<RangeControl
						label={ __( 'Columnas', 'vpinunf' ) }
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
						className="vpinunf-benefits-grid__title"
						style={ { marginBottom: '1rem' } }
						value={ sectionTitle }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
						placeholder={ __( 'Escribe el Título de sección...', 'vpinunf' ) }
					/>
					<RichText
						tagName="p"
						className="vpinunf-benefits-grid__subtitle"
						value={ sectionSubtitle }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
						onChange={ ( val ) => setAttributes( { sectionSubtitle: val } ) }
						placeholder={ __( 'Escribe un Subtítulo (opcional)...', 'vpinunf' ) }
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
