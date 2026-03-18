import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, InspectorControls, InnerBlocks, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'viceunf/schedule-card', { sessionLabel: __( 'Sesión 01', 'viceunf' ), date: '15 Mar', time: '09:00 – 11:00' } ],
	[ 'viceunf/schedule-card', { sessionLabel: __( 'Sesión 02', 'viceunf' ), date: '22 Mar', time: '09:00 – 11:00', headerColor: '#0e1422' } ],
	[ 'viceunf/schedule-card', { sessionLabel: __( 'Sesión 03', 'viceunf' ), date: '29 Mar', time: '09:00 – 11:00' } ],
];

function Edit( { attributes, setAttributes } ) {
	const { columns, sectionTitle } = attributes;
	const blockProps = useBlockProps( {
		className: 'viceunf-event-schedule-editor',
		style: { '--viceunf-sched-cols': columns },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'viceunf-event-schedule__grid' },
		{
			allowedBlocks: [ 'viceunf/schedule-card' ],
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'viceunf' ) }>
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
				<RichText
					tagName="h2"
					value={ sectionTitle }
					onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
					placeholder={ __( 'Título de sección...', 'viceunf' ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					style={ { textAlign: 'center', marginBottom: '4rem' } }
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
