import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, RangeControl, FormTokenField } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
	const { title, numberOfPosts, charLimit, categories } = attributes;

	// Obtenemos todas las categorías públicas para el selector
	const availableCategories = useSelect((select) => {
		return select('core').getEntityRecords('taxonomy', 'category', { per_page: -1 });
	}, []);

	// Transformamos las categorías para FormTokenField
	const categorySuggestions = availableCategories?.map(cat => cat.name) || [];
	const selectedCategoryNames = availableCategories
		?.filter(cat => categories.includes(cat.id))
		.map(cat => cat.name) || [];

	// Manejador del cambio de categorías, devuelve un array de IDs
	const onCategoryChange = (names) => {
		if (!availableCategories) return;
		const newCategoryIds = names.map(name => {
			const found = availableCategories.find(cat => cat.name === name);
			return found ? found.id : null;
		}).filter(id => id !== null);

		setAttributes({ categories: newCategoryIds });
	};

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Configuración del Bloque', 'viceunf')}>
					<TextControl
						label={__('Título', 'viceunf')}
						value={title}
						onChange={(val) => setAttributes({ title: val })}
					/>
					<RangeControl
						label={__('Número de Entradas', 'viceunf')}
						value={numberOfPosts}
						onChange={(val) => setAttributes({ numberOfPosts: val })}
						min={1}
						max={20}
					/>
					<RangeControl
						label={__('Límite de Caracteres (Título)', 'viceunf')}
						value={charLimit}
						onChange={(val) => setAttributes({ charLimit: val })}
						min={10}
						max={150}
					/>
					<FormTokenField
						label={__('Filtrar por Categoría (Opcional)', 'viceunf')}
						value={selectedCategoryNames}
						suggestions={categorySuggestions}
						onChange={onCategoryChange}
						__experimentalExpandOnFocus={true}
						help={__('Deja en blanco para mostrar todas las categorías.', 'viceunf')}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="viceunf-editor-recent-posts-preview" style={{ padding: '20px', border: '1px dashed #ccc', backgroundColor: '#fafafa' }}>
				<h3>{title || __('Entradas Recientes', 'viceunf')}</h3>
				<p><em>{__('Mostrando preview estática en el editor. El diseño real se renderizará en el frontend.', 'viceunf')}</em></p>
				<p>Nº de Posts: <strong>{numberOfPosts}</strong></p>
				<p>Categorías seleccionadas: <strong>{selectedCategoryNames.join(', ') || 'Ninguna (Todas)'}</strong></p>
			</div>
		</div>
	);
}
