import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { date, sessionLabel, time, location, headerColor } = attributes;

	const blockProps = useBlockProps( { className: 'vpinunf-schedule-card' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Datos de la Sesión (Legacy)', 'vpinunf' ) }>
					<p style={{ fontSize: '13px', color: '#666' }}>Esta tarjeta ahora es 100% WYSIWYG. Haz clic directamente sobre los textos en la tarjeta principal para editarlos.</p>
				</PanelBody>
				<PanelBody title={ __( 'Color de Cabecera', 'vpinunf' ) } initialOpen={ false }>
					<ColorPalette
						value={ headerColor }
						onChange={ ( val ) => setAttributes( { headerColor: val } ) }

					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="vpinunf-schedule-card__header" style={ { backgroundColor: headerColor } }>
					<div className="vpinunf-schedule-card__rings" aria-hidden="true">
						{ [ ...Array( 5 ) ].map( ( _, i ) => (
							<div key={ i } className="vpinunf-schedule-card__ring"></div>
						) ) }
					</div>
				</div>
				<div className="vpinunf-schedule-card__body">
					<div className="vpinunf-schedule-card__date">
						<RichText
							value={ date }
							onChange={ ( val ) => setAttributes( { date: val } ) }
							placeholder={ __( 'Fecha (ej: 15 Mar)', 'vpinunf' ) }
						/>
					</div>
					<RichText
						tagName="h4"
						className="vpinunf-schedule-card__session"
						value={ sessionLabel }
						onChange={ ( val ) => setAttributes( { sessionLabel: val } ) }
						placeholder={ __( 'Nombre / Número de sesión', 'vpinunf' ) }
					/>
					<p className="vpinunf-schedule-card__time" style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
						<i className="fa-regular fa-clock" aria-hidden="true" style={{ alignSelf: 'flex-start', marginTop: '4px' }}></i>
						<span style={{ flex: 1 }}>
							<RichText
								value={ time }
								onChange={ ( val ) => setAttributes( { time: val } ) }
								placeholder={ __( 'Horario (ej: 09:00 – 11:00)', 'vpinunf' ) }
							/>
						</span>
					</p>
					<p className="vpinunf-schedule-card__location" style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
						<i className="fa-solid fa-location-dot" aria-hidden="true" style={{ alignSelf: 'flex-start', marginTop: '4px' }}></i>
						<span style={{ flex: 1 }}>
							<RichText
								value={ location }
								onChange={ ( val ) => setAttributes( { location: val } ) }
								placeholder={ __( 'Lugar (opcional)', 'vpinunf' ) }
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
