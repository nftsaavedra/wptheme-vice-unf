import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
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
					<h2 style={ { textAlign: 'center', marginBottom: '4rem' } }>{ sectionTitle }</h2>
				) }
				<div
					className="viceunf-event-schedule__grid"
					style={ { display: 'grid', gridTemplateColumns: `repeat(${ columns }, 1fr)`, gap: '2.4rem' } }
				>
					<InnerBlocks
						allowedBlocks={ [ 'viceunf/schedule-card' ] }
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
