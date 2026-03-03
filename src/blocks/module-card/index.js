import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	Button,
	ColorPicker,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function SemiCircleProgress( { percent, color, icon } ) {
	const r = 40;
	const circ = Math.PI * r;
	const offset = circ - ( circ * Math.min( 100, Math.max( 0, percent ) ) ) / 100;
	return (
		<div style={ { position: 'relative', width: '100px', height: '60px', margin: '0 auto 1.6rem' } }>
			<svg viewBox="0 0 100 56" style={ { width: '100px', height: '56px', overflow: 'visible' } }>
				<path
					d="M 10,50 A 40,40 0 0,1 90,50"
					fill="none"
					stroke="#e5e7eb"
					strokeWidth="8"
					strokeLinecap="round"
				/>
				<path
					d="M 10,50 A 40,40 0 0,1 90,50"
					fill="none"
					stroke={ color }
					strokeWidth="8"
					strokeLinecap="round"
					strokeDasharray={ circ }
					strokeDashoffset={ offset }
					style={ { transition: 'stroke-dashoffset 0.6s ease' } }
				/>
			</svg>
			<div style={ {
				position: 'absolute',
				bottom: '4px',
				left: '50%',
				transform: 'translateX(-50%)',
				fontSize: '1.8rem',
				color,
			} }>
				<i className={ icon } aria-hidden="true"></i>
			</div>
		</div>
	);
}

function Edit( { attributes, setAttributes, context } ) {
	const { label, icon, progressPercent, bulletPoints, cardColor } = attributes;
	const progressColor = '#ff4700';
	const [ newBullet, setNewBullet ] = useState( '' );

	const blockProps = useBlockProps( {
		className: 'viceunf-module-card',
		style: { backgroundColor: cardColor },
	} );

	const addBullet = () => {
		if ( newBullet.trim() ) {
			setAttributes( { bulletPoints: [ ...bulletPoints, newBullet.trim() ] } );
			setNewBullet( '' );
		}
	};

	const removeBullet = ( index ) => {
		setAttributes( { bulletPoints: bulletPoints.filter( ( _, i ) => i !== index ) } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contenido del Módulo', 'viceunf' ) }>
					<TextControl
						label={ __( 'Etiqueta (ej: 01 sesión)', 'viceunf' ) }
						value={ label }
						onChange={ ( val ) => setAttributes( { label: val } ) }
					/>
					<TextControl
						label={ __( 'Clase de ícono FA', 'viceunf' ) }
						value={ icon }
						onChange={ ( val ) => setAttributes( { icon: val } ) }
					/>
					<RangeControl
						label={ __( '% de progreso visual', 'viceunf' ) }
						value={ progressPercent }
						onChange={ ( val ) => setAttributes( { progressPercent: val } ) }
						min={ 0 }
						max={ 100 }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Puntos Clave', 'viceunf' ) } initialOpen={ false }>
					{ bulletPoints.map( ( point, i ) => (
						<div key={ i } style={ { display: 'flex', gap: '8px', marginBottom: '6px', alignItems: 'center' } }>
							<span style={ { flex: 1, fontSize: '12px' } }>{ point }</span>
							<Button isSmall isDestructive variant="tertiary" onClick={ () => removeBullet( i ) }>✕</Button>
						</div>
					) ) }
					<TextControl
						label={ __( 'Nuevo punto clave', 'viceunf' ) }
						value={ newBullet }
						onChange={ setNewBullet }
					/>
					<Button variant="secondary" onClick={ addBullet } style={ { width: '100%', justifyContent: 'center' } }>
						{ __( '+ Agregar', 'viceunf' ) }
					</Button>
				</PanelBody>
				<PanelBody title={ __( 'Color de la Tarjeta', 'viceunf' ) } initialOpen={ false }>
					<ColorPicker
						color={ cardColor }
						onChange={ ( val ) => setAttributes( { cardColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ label && <p className="viceunf-module-card__label">{ label }</p> }
				<SemiCircleProgress percent={ progressPercent } color={ progressColor } icon={ icon } />
				<div className="viceunf-module-card__body">
					{ bulletPoints.length > 0 && (
						<ul className="viceunf-module-card__bullets">
							{ bulletPoints.map( ( point, i ) => (
								<li key={ i }>{ point }</li>
							) ) }
						</ul>
					) }
					{ bulletPoints.length === 0 && (
						<p style={ { color: 'rgba(255,255,255,0.5)', fontSize: '1.3rem', textAlign: 'center' } }>
							{ __( 'Agrega puntos clave desde el panel lateral →', 'viceunf' ) }
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
