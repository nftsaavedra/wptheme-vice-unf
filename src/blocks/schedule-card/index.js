import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { date, sessionLabel, time, location, headerColor } = attributes;

	const blockProps = useBlockProps( { className: 'viceunf-schedule-card' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Datos de la Sesión (Legacy)', 'viceunf' ) }>
					<p style={{ fontSize: '13px', color: '#666' }}>Esta tarjeta ahora es 100% WYSIWYG. Haz clic directamente sobre los textos en la tarjeta principal para editarlos.</p>
				</PanelBody>
				<PanelBody title={ __( 'Color de Cabecera', 'viceunf' ) } initialOpen={ false }>
					<ColorPalette
						value={ headerColor }
						onChange={ ( val ) => setAttributes( { headerColor: val } ) }

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
					<div className="viceunf-schedule-card__date">
						<RichText
							value={ date }
							onChange={ ( val ) => setAttributes( { date: val } ) }
							placeholder={ __( 'Fecha (ej: 15 Mar)', 'viceunf' ) }
						/>
					</div>
					<RichText
						tagName="h4"
						className="viceunf-schedule-card__session"
						value={ sessionLabel }
						onChange={ ( val ) => setAttributes( { sessionLabel: val } ) }
						placeholder={ __( 'Nombre / Número de sesión', 'viceunf' ) }
					/>
					<p className="viceunf-schedule-card__time" style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
						<i className="fa-regular fa-clock" aria-hidden="true" style={{ alignSelf: 'flex-start', marginTop: '4px' }}></i>
						<span style={{ flex: 1 }}>
							<RichText
								value={ time }
								onChange={ ( val ) => setAttributes( { time: val } ) }
								placeholder={ __( 'Horario (ej: 09:00 – 11:00)', 'viceunf' ) }
							/>
						</span>
					</p>
					<p className="viceunf-schedule-card__location" style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
						<i className="fa-solid fa-location-dot" aria-hidden="true" style={{ alignSelf: 'flex-start', marginTop: '4px' }}></i>
						<span style={{ flex: 1 }}>
							<RichText
								value={ location }
								onChange={ ( val ) => setAttributes( { location: val } ) }
								placeholder={ __( 'Lugar (opcional)', 'viceunf' ) }
							/>
						</span>
					</p>
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
