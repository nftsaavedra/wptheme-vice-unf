import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, InspectorControls, InnerBlocks, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const TEMPLATE = [
	[ 'vpinunf/schedule-card', { sessionLabel: __( 'Sesión 01', 'vpinunf' ), date: '15 Mar', time: '09:00 – 11:00' } ],
	[ 'vpinunf/schedule-card', { sessionLabel: __( 'Sesión 02', 'vpinunf' ), date: '22 Mar', time: '09:00 – 11:00', headerColor: '#0e1422' } ],
	[ 'vpinunf/schedule-card', { sessionLabel: __( 'Sesión 03', 'vpinunf' ), date: '29 Mar', time: '09:00 – 11:00' } ],
];

function Edit( { attributes, setAttributes } ) {
	const { columns, sectionTitle } = attributes;
	const blockProps = useBlockProps( {
		className: 'vpinunf-event-schedule-editor',
		style: { '--vpinunf-sched-cols': columns },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'vpinunf-event-schedule__grid' },
		{
			allowedBlocks: [ 'vpinunf/schedule-card' ],
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración', 'vpinunf' ) }>
					<RangeControl
						label={ __( 'Columnas', 'vpinunf' ) }
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
					placeholder={ __( 'Título de sección...', 'vpinunf' ) }
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
