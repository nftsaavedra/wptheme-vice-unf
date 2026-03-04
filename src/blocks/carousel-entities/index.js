import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ComboboxControl, RangeControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { sectionTitle, postTypeOrigin, itemsLimit } = attributes;

        // Extraer los post types de la REST API de WordPress
        const postTypes = useSelect((select) => {
            // Documentado en wp.data | Trae todos los CPTs
            return select('core').getPostTypes({ per_page: -1 });
        }, []);

        // Excluir post types nativos que no aportan al carrusel
        const excludedTypes = ['attachment', 'page', 'post', 'wp_template', 'wp_template_part', 'wp_navigation', 'wp_block', 'nav_menu_item', 'wp_global_styles', 'wp_font_family', 'wp_font_face', 'user_request'];

        // Filtrar y mapear: Sólo tipos de posts para "Entidades", evitando core
        // Se quita el `pt.viewable` estricto porque CPTs como 'socio' son internos (public: false) pero válidos para bloques
        const postTypeOptions = (postTypes || [])
            .filter(pt => !excludedTypes.includes(pt.slug))
            .map(pt => ({
                label: `${pt?.name || pt?.slug} (${pt.slug})`,
                value: pt.slug
            }));

        const blockProps = useBlockProps({
            className: 'viceunf-carousel-entities-editor-preview'
        });

        // Buscamos el nombre del CPT seleccionado para mostrar en el preview
        const selectedPostTypeLabel = postTypeOptions.find(opt => opt.value === postTypeOrigin)?.label || postTypeOrigin;

        return (
            <div { ...blockProps }>
                <InspectorControls>
                    <PanelBody title="Configuración del Carrusel" initialOpen={true}>
                        <TextControl
                            label="Título de la Sección"
                            value={sectionTitle}
                            onChange={(val) => setAttributes({ sectionTitle: val })}
                        />
                        <ComboboxControl
                            label="Tipo de Contenido (Post Type)"
                            value={postTypeOrigin}
                            options={postTypeOptions}
                            onChange={(val) => setAttributes({ postTypeOrigin: val })}
                            help="Escribe para buscar el Post Type."
                        />
                        <RangeControl
                            label="Límite de Elementos (-1 para todos)"
                            value={itemsLimit}
                            onChange={(val) => setAttributes({ itemsLimit: val })}
                            min={-1}
                            max={20}
                        />
                    </PanelBody>
                </InspectorControls>
                
                <div style={{ padding: '20px', border: '2px dashed #002244', textAlign: 'center', backgroundColor: '#f1f5f9' }}>
                    <h3 style={{ margin: 0, color: '#002244' }}>[Bloque: Carrusel de Entidades]</h3>
                    <p style={{ margin: '10px 0 0', fontSize: '14px', color: '#666' }}>
                        <strong>Título:</strong> {sectionTitle} <br/>
                        <strong>Origen de Datos:</strong> {selectedPostTypeLabel} <br/>
                        <strong>Cargará:</strong> {itemsLimit === -1 ? 'Todos' : itemsLimit} items.
                    </p>
                    <p style={{ margin: '10px 0 0', fontSize: '12px', color: '#999' }}>El carrusel (Swiper JS) se renderizará automáticamente en la vista pública.</p>
                </div>
            </div>
        );
    },
    save: () => {
        return null; // Block dinámico (Server-side rendering)
    }
});
