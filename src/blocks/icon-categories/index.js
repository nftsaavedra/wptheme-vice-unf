import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	Button,
	RangeControl,
	SelectControl,
	ColorPicker,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const DEFAULT_ITEM = { icon: 'fa-solid fa-circle', label: '', url: '' };

function Edit( { attributes, setAttributes } ) {
	const { items, iconBgColor, layout, columns } = attributes;
	const [ editingIndex, setEditingIndex ] = useState( null );

	const blockProps = useBlockProps( {
		className: `viceunf-icon-categories viceunf-icon-categories--${ layout }`,
	} );

	const addItem = () => {
		setAttributes( { items: [ ...items, { ...DEFAULT_ITEM } ] } );
		setEditingIndex( items.length );
	};

	const removeItem = ( index ) => {
		const updated = items.filter( ( _, i ) => i !== index );
		setAttributes( { items: updated } );
		setEditingIndex( null );
	};

	const updateItem = ( index, field, value ) => {
		const updated = items.map( ( item, i ) =>
			i === index ? { ...item, [ field ]: value } : item
		);
		setAttributes( { items: updated } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Diseño', 'viceunf' ) }>
					<SelectControl
						label={ __( 'Disposición', 'viceunf' ) }
						value={ layout }
						options={ [
							{ label: __( 'Fila (horizontal)', 'viceunf' ), value: 'row' },
							{ label: __( 'Grilla', 'viceunf' ), value: 'grid' },
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
					{ layout === 'grid' && (
						<RangeControl
							label={ __( 'Columnas (desktop)', 'viceunf' ) }
							value={ columns }
							onChange={ ( val ) => setAttributes( { columns: val } ) }
							min={ 3 }
							max={ 8 }
						/>
					) }
					<p style={ { fontSize: '12px', marginTop: '16px', marginBottom: '8px' } }>
						{ __( 'Color de fondo de los círculos', 'viceunf' ) }
					</p>
					<ColorPicker
						color={ iconBgColor }
						onChange={ ( val ) => setAttributes( { iconBgColor: val } ) }
						enableAlpha={ false }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Ítems', 'viceunf' ) } initialOpen={ true }>
					{ items.map( ( item, index ) => (
						<div
							key={ index }
							style={ { borderBottom: '1px solid #eee', paddingBottom: '12px', marginBottom: '12px' } }
						>
							<div style={ { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' } }>
								<strong style={ { fontSize: '12px' } }>{ item.label || `${ __( 'Ítem', 'viceunf' ) } ${ index + 1 }` }</strong>
								<div>
									<Button
										isSmall
										variant="tertiary"
										onClick={ () => setEditingIndex( editingIndex === index ? null : index ) }
									>
										{ editingIndex === index ? __( 'Cerrar', 'viceunf' ) : __( 'Editar', 'viceunf' ) }
									</Button>
									<Button
										isSmall
										isDestructive
										variant="tertiary"
										onClick={ () => removeItem( index ) }
										style={ { marginLeft: '4px' } }
									>
										✕
									</Button>
								</div>
							</div>
							{ editingIndex === index && (
								<>
									<TextControl
										label={ __( 'Etiqueta', 'viceunf' ) }
										value={ item.label }
										onChange={ ( val ) => updateItem( index, 'label', val ) }
									/>
									<TextControl
										label={ __( 'Clase de ícono FA (ej: fa-solid fa-code)', 'viceunf' ) }
										value={ item.icon }
										onChange={ ( val ) => updateItem( index, 'icon', val ) }
									/>
									<TextControl
										label={ __( 'URL (opcional)', 'viceunf' ) }
										value={ item.url }
										onChange={ ( val ) => updateItem( index, 'url', val ) }
										type="url"
									/>
								</>
							) }
						</div>
					) ) }
					<Button variant="primary" onClick={ addItem } style={ { width: '100%', justifyContent: 'center' } }>
						{ __( '+ Agregar ítem', 'viceunf' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }
				style={ { '--viceunf-icon-bg': iconBgColor, '--viceunf-icon-cols': columns } }
			>
				{ items.length === 0 && (
					<p style={ { textAlign: 'center', color: '#999', padding: '2rem', width: '100%' } }>
						{ __( 'Agrega ítems desde el panel lateral →', 'viceunf' ) }
					</p>
				) }
				{ items.map( ( item, index ) => {
					const iconClass = ( item.icon || 'fa-solid fa-circle' ).replace( /[^a-zA-Z0-9\s\-]/g, '' );
					return (
						<div key={ index } className="viceunf-icon-categories__item">
							<div
								className="viceunf-icon-categories__circle"
								style={ { backgroundColor: iconBgColor } }
							>
								<i className={ iconClass } aria-hidden="true"></i>
							</div>
							<span className="viceunf-icon-categories__label">{ item.label }</span>
						</div>
					);
				} ) }
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
