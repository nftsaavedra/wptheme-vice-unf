import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, RangeControl, FormTokenField } from '@wordpress/components';
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
				<PanelBody title={__('Configuración del Bloque', 'vpinunf')}>
					<RangeControl
						label={__('Número de Entradas', 'vpinunf')}
						value={numberOfPosts}
						onChange={(val) => setAttributes({ numberOfPosts: val })}
						min={1}
						max={20}
					/>
					<RangeControl
						label={__('Límite de Caracteres (Título)', 'vpinunf')}
						value={charLimit}
						onChange={(val) => setAttributes({ charLimit: val })}
						min={10}
						max={150}
					/>
					<FormTokenField
						label={__('Filtrar por Categoría (Opcional)', 'vpinunf')}
						value={selectedCategoryNames}
						suggestions={categorySuggestions}
						onChange={onCategoryChange}
						__experimentalExpandOnFocus={true}
						help={__('Deja en blanco para mostrar todas las categorías.', 'vpinunf')}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="vpinunf-editor-recent-posts-preview" style={{ padding: '20px', border: '1px dashed #ccc', backgroundColor: '#fafafa' }}>
				<RichText
					tagName="h3"
					value={title}
					onChange={(val) => setAttributes({ title: val })}
					placeholder={__('Escribe el título...', 'vpinunf')}
					allowedFormats={['core/bold', 'core/italic', 'core/link']}
				/>
				<p><em>{__('Mostrando preview estática en el editor. El diseño real se renderizará en el frontend.', 'vpinunf')}</em></p>
				<p>Nº de Posts: <strong>{numberOfPosts}</strong></p>
				<p>Categorías seleccionadas: <strong>{selectedCategoryNames.join(', ') || 'Ninguna (Todas)'}</strong></p>
			</div>
		</div>
	);
}
