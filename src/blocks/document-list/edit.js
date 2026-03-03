import { useSelect } from '@wordpress/data';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, CheckboxControl, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
  const { postType, taxonomy, selectedCategories } = attributes;

  // Obtener tipos de post públicos
  const postTypes = useSelect((select) => {
    const types = select('core').getPostTypes({ per_page: -1 });
    return types ? types.filter(pt => pt.viewable && pt.slug !== 'attachment' && pt.slug !== 'page' && pt.slug !== 'post') : null;
  }, []);

  // Obtener taxonomías asociadas al tipo de post seleccionado
  const taxonomies = useSelect((select) => {
    if (!postType) return null;
    const taxs = select('core').getTaxonomies({ type: postType, per_page: -1 });
    return taxs ? taxs.filter(tax => tax.visibility && tax.visibility.show_ui) : null;
  }, [postType]);

  // Si cambia el postType, opcionalmente resetear taxonomía si la actual no es válida
  if (taxonomies && taxonomies.length > 0 && !taxonomies.find(tax => tax.slug === taxonomy)) {
    setAttributes({ taxonomy: taxonomies[0].slug, selectedCategories: [] });
  }

  // Obtener las categorías de la taxonomía seleccionada
  const categories = useSelect((select) => {
    if (!taxonomy) return null;
    return select('core').getEntityRecords('taxonomy', taxonomy, { per_page: -1 });
  }, [taxonomy]);

  const postTypeOptions = postTypes ? postTypes.map(pt => ({ label: pt.name, value: pt.slug })) : [];
  const taxonomyOptions = taxonomies ? taxonomies.map(tax => ({ label: tax.name, value: tax.slug })) : [];

  const onCategoryChange = (isChecked, categoryId) => {
    const newSelectedCategories = isChecked
      ? [...selectedCategories, categoryId]
      : selectedCategories.filter((id) => id !== categoryId);
    setAttributes({ selectedCategories: newSelectedCategories });
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Configuración del Listado', 'viceunf')}>
          {!postTypes && <Spinner />}
          {postTypes && (
            <SelectControl
              label={__('Tipo de Contenido (Post Type)', 'viceunf')}
              value={postType}
              options={postTypeOptions}
              onChange={(value) => setAttributes({ postType: value, selectedCategories: [] })}
            />
          )}

          {!taxonomies && postType && <Spinner />}
          {taxonomies && taxonomies.length > 0 && (
            <SelectControl
              label={__('Taxonomía de Categorización', 'viceunf')}
              value={taxonomy}
              options={taxonomyOptions}
              onChange={(value) => setAttributes({ taxonomy: value, selectedCategories: [] })}
            />
          )}
        </PanelBody>

        <PanelBody title={__('Filtro de Categorías', 'viceunf')} initialOpen={false}>
          {!categories && taxonomy && <Spinner />}
          
          {categories && categories.length === 0 && (
            <p>{__('No se encontraron categorías. Por favor, cree algunas primero.', 'viceunf')}</p>
          )}

          {categories && categories.length > 0 && (
            <>
              <p>{__('Muestra documentos de las categorías seleccionadas. Si no se selecciona ninguna, se mostrará el árbol completo.', 'viceunf')}</p>
              {categories.map((category) => (
                <CheckboxControl
                  key={category.id}
                  label={category.name}
                  checked={selectedCategories.includes(category.id)}
                  onChange={(isChecked) => onCategoryChange(isChecked, category.id)}
                />
              ))}
            </>
          )}
        </PanelBody>
      </InspectorControls>

      <div className="viceunf-block-placeholder" style={{ padding: '20px', border: '1px dashed #ccc', backgroundColor: '#f9f9f9', textAlign: 'center' }}>
        <h3>{__('Lista de Documentos', 'viceunf')}</h3>
        <p>
          {__('Tipo:', 'viceunf')} <strong>{postType}</strong> | __('Taxonomía:', 'viceunf')} <strong>{taxonomy}</strong>
        </p>
        <p>
          {selectedCategories.length > 0
            ? __(`Mostrando ${selectedCategories.length} categoría(s) específica(s). Se renderizará en formato tabla/lista.`, 'viceunf')
            : __('Mostrando todo el árbol jerárquico de categorías.', 'viceunf')}
        </p>
        <p style={{ fontSize: '12px', color: '#666' }}>{__('El renderizado final con estilos y documentos se verá en la página publicada.', 'viceunf')}</p>
      </div>
    </>
  );
}
