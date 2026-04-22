import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	Button,
	RangeControl,
	SelectControl,
	ColorPalette,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const DEFAULT_ITEM = { icon: 'fa-solid fa-circle', label: '', url: '' };

function Edit( { attributes, setAttributes } ) {
	const { items, iconBgColor, layout, columns } = attributes;
	const [ editingIndex, setEditingIndex ] = useState( null );

	const blockProps = useBlockProps( {
		className: `vpinunf-icon-categories vpinunf-icon-categories--${ layout }`,
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
				<PanelBody title={ __( 'Diseño', 'vpinunf' ) }>
					<SelectControl
						label={ __( 'Disposición', 'vpinunf' ) }
						value={ layout }
						options={ [
							{ label: __( 'Fila (horizontal)', 'vpinunf' ), value: 'row' },
							{ label: __( 'Grilla', 'vpinunf' ), value: 'grid' },
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
					{ layout === 'grid' && (
						<RangeControl
							label={ __( 'Columnas (desktop)', 'vpinunf' ) }
							value={ columns }
							onChange={ ( val ) => setAttributes( { columns: val } ) }
							min={ 3 }
							max={ 8 }
						/>
					) }
					<p style={ { fontSize: '12px', marginTop: '16px', marginBottom: '8px' } }>
						{ __( 'Color de fondo de los círculos', 'vpinunf' ) }
					</p>
					<ColorPalette
						value={ iconBgColor }
						onChange={ ( val ) => setAttributes( { iconBgColor: val } ) }

					/>
				</PanelBody>
				<PanelBody title={ __( 'Ítems', 'vpinunf' ) } initialOpen={ true }>
					{ items.map( ( item, index ) => (
						<div
							key={ index }
							style={ { borderBottom: '1px solid #eee', paddingBottom: '12px', marginBottom: '12px' } }
						>
							<div style={ { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' } }>
								<strong style={ { fontSize: '12px' } }>{ item.label || `${ __( 'Ítem', 'vpinunf' ) } ${ index + 1 }` }</strong>
								<div>
									<Button
										isSmall
										variant="tertiary"
										onClick={ () => setEditingIndex( editingIndex === index ? null : index ) }
									>
										{ editingIndex === index ? __( 'Cerrar', 'vpinunf' ) : __( 'Editar', 'vpinunf' ) }
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
										label={ __( 'Etiqueta', 'vpinunf' ) }
										value={ item.label }
										onChange={ ( val ) => updateItem( index, 'label', val ) }
									/>
									<TextControl
										label={ __( 'Clase de ícono FA (ej: fa-solid fa-code)', 'vpinunf' ) }
										value={ item.icon }
										onChange={ ( val ) => updateItem( index, 'icon', val ) }
									/>
									<TextControl
										label={ __( 'URL (opcional)', 'vpinunf' ) }
										value={ item.url }
										onChange={ ( val ) => updateItem( index, 'url', val ) }
										type="url"
									/>
								</>
							) }
						</div>
					) ) }
					<Button variant="primary" onClick={ addItem } style={ { width: '100%', justifyContent: 'center' } }>
						{ __( '+ Agregar ítem', 'vpinunf' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }
				style={ { '--vpinunf-icon-bg': iconBgColor, '--vpinunf-icon-cols': columns } }
			>
				{ items.length === 0 && (
					<p style={ { textAlign: 'center', color: '#999', padding: '2rem', width: '100%' } }>
						{ __( 'Agrega ítems desde el panel lateral →', 'vpinunf' ) }
					</p>
				) }
				{ items.map( ( item, index ) => {
					const iconClass = ( item.icon || 'fa-solid fa-circle' ).replace( /[^a-zA-Z0-9\s\-]/g, '' );
					return (
						<div key={ index } className="vpinunf-icon-categories__item">
							<div
								className="vpinunf-icon-categories__circle"
								style={ { backgroundColor: iconBgColor } }
							>
								<i className={ iconClass } aria-hidden="true"></i>
							</div>
							<RichText
								tagName="span"
								className="vpinunf-icon-categories__label"
								value={ item.label }
								onChange={ ( val ) => updateItem( index, 'label', val ) }
								placeholder={ __( 'Nombre de categoría', 'vpinunf' ) }
								withoutInteractiveFormatting
							/>
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
