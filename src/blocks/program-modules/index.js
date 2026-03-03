import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl, ColorPicker } from '@wordpress/components';
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

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
					<TextControl
						label={ __( 'Título de sección (opcional)', 'viceunf' ) }
						value={ sectionTitle }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					/>
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
					<ColorPicker
						color={ progressColor }
						onChange={ ( val ) => setAttributes( { progressColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps } style={ { '--viceunf-progress-color': progressColor, '--viceunf-mod-cols': columns } }>
				{ sectionTitle && (
					<h2 style={ { textAlign: 'center', marginBottom: '4rem', color: '#0e1422' } }>{ sectionTitle }</h2>
				) }
				<div className="viceunf-program-modules__grid">
					<InnerBlocks
						allowedBlocks={ [ 'viceunf/module-card' ] }
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
