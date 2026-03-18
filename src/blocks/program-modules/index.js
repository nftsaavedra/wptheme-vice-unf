import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, InspectorControls, InnerBlocks, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'viceunf/module-card', { label: __( '01 sesión', 'viceunf' ), icon: 'fa-solid fa-rocket', progressPercent: 60 } ],
	[ 'viceunf/module-card', { label: __( '02 sesiones', 'viceunf' ), icon: 'fa-solid fa-lightbulb', progressPercent: 75 } ],
	[ 'viceunf/module-card', { label: __( '03 sesiones', 'viceunf' ), icon: 'fa-solid fa-trophy', progressPercent: 90 } ],
];

function Edit( { attributes, setAttributes } ) {
	const { columns, progressColor, sectionTitle } = attributes;
	const blockProps = useBlockProps( { className: 'viceunf-program-modules-editor' } );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'viceunf-program-modules__grid' },
		{
			allowedBlocks: [ 'viceunf/module-card' ],
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
					<RangeControl
						label={ __( 'Módulos por fila', 'viceunf' ) }
						value={ columns }
						onChange={ ( val ) => setAttributes( { columns: val } ) }
						min={ 2 }
						max={ 4 }
					/>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>
						{ __( 'Color del indicador de progreso', 'viceunf' ) }
					</p>
					<ColorPalette
						value={ progressColor }
						onChange={ ( val ) => setAttributes( { progressColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--viceunf-progress-color': progressColor, '--viceunf-mod-cols': columns } }>
				<RichText
					tagName="h2"
					value={ sectionTitle }
					onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					placeholder={ __( 'Título de sección...', 'viceunf' ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					style={ { textAlign: 'center', marginBottom: '4rem', color: '#0e1422' } }
				/>
				<div { ...innerBlocksProps }></div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
