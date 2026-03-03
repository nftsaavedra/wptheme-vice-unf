import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ColorPicker } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { date, sessionLabel, time, location, headerColor } = attributes;

	const blockProps = useBlockProps( { className: 'viceunf-schedule-card' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Datos de la Sesión', 'viceunf' ) }>
					<TextControl
						label={ __( 'Nombre / Número de sesión', 'viceunf' ) }
						value={ sessionLabel }
						onChange={ ( val ) => setAttributes( { sessionLabel: val } ) }
					/>
					<TextControl
						label={ __( 'Fecha (texto libre, ej: 15 Mar)', 'viceunf' ) }
						value={ date }
						onChange={ ( val ) => setAttributes( { date: val } ) }
					/>
					<TextControl
						label={ __( 'Horario (ej: 09:00 – 11:00)', 'viceunf' ) }
						value={ time }
						onChange={ ( val ) => setAttributes( { time: val } ) }
					/>
					<TextControl
						label={ __( 'Lugar (opcional)', 'viceunf' ) }
						value={ location }
						onChange={ ( val ) => setAttributes( { location: val } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Color de Cabecera', 'viceunf' ) } initialOpen={ false }>
					<ColorPicker
						color={ headerColor }
						onChange={ ( val ) => setAttributes( { headerColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="viceunf-schedule-card__header" style={ { backgroundColor: headerColor } }>
					<div className="viceunf-schedule-card__rings" aria-hidden="true">
						{ [ ...Array( 5 ) ].map( ( _, i ) => (
							<div key={ i } className="viceunf-schedule-card__ring"></div>
						) ) }
					</div>
				</div>
				<div className="viceunf-schedule-card__body">
					{ date && <div className="viceunf-schedule-card__date">{ date }</div> }
					{ sessionLabel && <h4 className="viceunf-schedule-card__session">{ sessionLabel }</h4> }
					{ time && (
						<p className="viceunf-schedule-card__time">
							<i className="fa-regular fa-clock" aria-hidden="true"></i> { time }
						</p>
					) }
					{ location && (
						<p className="viceunf-schedule-card__location">
							<i className="fa-solid fa-location-dot" aria-hidden="true"></i> { location }
						</p>
					) }
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
