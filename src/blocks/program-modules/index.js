import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, InspectorControls, InnerBlocks, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'vpinunf/module-card', { label: __( '01 sesión', 'vpinunf' ), icon: 'fa-solid fa-rocket', progressPercent: 60 } ],
	[ 'vpinunf/module-card', { label: __( '02 sesiones', 'vpinunf' ), icon: 'fa-solid fa-lightbulb', progressPercent: 75 } ],
	[ 'vpinunf/module-card', { label: __( '03 sesiones', 'vpinunf' ), icon: 'fa-solid fa-trophy', progressPercent: 90 } ],
];

function Edit( { attributes, setAttributes } ) {
	const { columns, progressColor, sectionTitle } = attributes;
	const blockProps = useBlockProps( { className: 'vpinunf-program-modules-editor' } );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'vpinunf-program-modules__grid' },
		{
			allowedBlocks: [ 'vpinunf/module-card' ],
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'vpinunf' ) }>
					<RangeControl
						label={ __( 'Módulos por fila', 'vpinunf' ) }
						value={ columns }
						onChange={ ( val ) => setAttributes( { columns: val } ) }
						min={ 2 }
						max={ 4 }
					/>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>
						{ __( 'Color del indicador de progreso', 'vpinunf' ) }
					</p>
					<ColorPalette
						value={ progressColor }
						onChange={ ( val ) => setAttributes( { progressColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--vpinunf-progress-color': progressColor, '--vpinunf-mod-cols': columns } }>
				<RichText
					tagName="h2"
					value={ sectionTitle }
					onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					placeholder={ __( 'Título de sección...', 'vpinunf' ) }
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
