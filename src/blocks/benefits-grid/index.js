import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { columns, sectionTitle, sectionSubtitle } = attributes;

	const TEMPLATE = [
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 1', 'viceunf' ), icon: 'fa-solid fa-rocket' } ],
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 2', 'viceunf' ), icon: 'fa-solid fa-lightbulb' } ],
		[ 'viceunf/benefit-card', { title: __( 'Beneficio 3', 'viceunf' ), icon: 'fa-solid fa-graduation-cap' } ],
	];

	const blockProps = useBlockProps( {
		className: 'viceunf-benefits-grid-editor',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración de Sección', 'viceunf' ) }>
					<TextControl
						label={ __( 'Título de sección', 'viceunf' ) }
						value={ sectionTitle }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					/>
					<TextControl
						label={ __( 'Subtítulo de sección', 'viceunf' ) }
						value={ sectionSubtitle }
						onChange={ ( val ) => setAttributes( { sectionSubtitle: val } ) }
					/>
					<RangeControl
						label={ __( 'Columnas', 'viceunf' ) }
						value={ columns }
						onChange={ ( val ) => setAttributes( { columns: val } ) }
						min={ 2 }
						max={ 4 }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ sectionTitle && (
					<div style={ { textAlign: 'center', marginBottom: '3.2rem' } }>
						<h2 style={ { color: '#0e1422', marginBottom: '1rem' } }>{ sectionTitle }</h2>
						{ sectionSubtitle && <p style={ { color: '#666' } }>{ sectionSubtitle }</p> }
					</div>
				) }
				<div
					style={ {
						display: 'grid',
						gridTemplateColumns: `repeat(${ columns }, 1fr)`,
						gap: '2.4rem',
					} }
				>
					<InnerBlocks
						allowedBlocks={ [ 'viceunf/benefit-card' ] }
						template={ TEMPLATE }
						orientation="horizontal"
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
