import { registerBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from '../block.json';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available attributes.
 * @param {Function} props.setAttributes Function to update block attributes.
 * @return {WPElement} Element to render.
 */
function Edit({ attributes, setAttributes }) {
  const { selectedCategories } = attributes;

  // Hook de WordPress para obtener datos. Aquí pedimos todas las categorías de la taxonomía 'categoria_reglamento'.
  const allCategories = useSelect((select) => {
    return select('core').getEntityRecords('taxonomy', 'categoria_reglamento');
  }, []);

  // Función que se ejecuta cuando se marca o desmarca una casilla.
  const onCategoryChange = (isChecked, categoryId) => {
    const newSelectedCategories = isChecked
      ? [...selectedCategories, categoryId]
      : selectedCategories.filter((id) => id !== categoryId);
    setAttributes({ selectedCategories: newSelectedCategories });
  };

  return (
    <>
      {/* Controles que aparecen en la barra lateral del editor */}
      <InspectorControls>
        <PanelBody title={__('Selección de Categorías', 'viceunf')}>
          {/* Muestra un spinner mientras se cargan las categorías */}
          {!allCategories && <Spinner />}

          {/* Muestra un mensaje si no hay categorías creadas */}
          {allCategories && allCategories.length === 0 && (
            <p>{__('No se encontraron categorías. Por favor, cree algunas primero.', 'viceunf')}</p>
          )}

          {/* Muestra la lista de casillas si hay categorías */}
          {allCategories && allCategories.length > 0 && (
            <>
              <p>{__('Muestra reglamentos de las categorías seleccionadas. Si no se selecciona ninguna, se mostrarán todas.', 'viceunf')}</p>
              {allCategories.map((category) => (
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

      {/* Esto es lo que se ve en el área principal del editor */}
      <div className="viceunf-block-placeholder">
        <h3>{__('Grid de Reglamentos', 'viceunf')}</h3>
        <p>
          {selectedCategories.length > 0
            ? __(`Mostrando ${selectedCategories.length} categoría(s) seleccionada(s).`, 'viceunf')
            : __('Mostrando todas las categorías.', 'viceunf')}
        </p>
        <p>{__('Los ajustes para seleccionar categorías se encuentran en la barra lateral.', 'viceunf')}</p>
      </div>
    </>
  );
}

/**
 * Registra el bloque con WordPress.
 */
registerBlockType(metadata.name, {
  edit: Edit,
  save: () => null, // La renderización se hace en PHP (bloque dinámico), por lo que save() es nulo.
});