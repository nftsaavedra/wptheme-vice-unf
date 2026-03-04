import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, RangeControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { sectionTitle, postTypeOrigin, itemsLimit } = attributes;

        const blockProps = useBlockProps({
            className: 'viceunf-carousel-entities-editor-preview'
        });

        return (
            <div { ...blockProps }>
                <InspectorControls>
                    <PanelBody title="Configuración del Carrusel" initialOpen={true}>
                        <TextControl
                            label="Título de la Sección"
                            value={sectionTitle}
                            onChange={(val) => setAttributes({ sectionTitle: val })}
                        />
                        <SelectControl
                            label="Tipo de Contenido (Post Type)"
                            value={postTypeOrigin}
                            options={[
                                { label: 'Socios', value: 'socio' },
                                { label: 'Convenios', value: 'convenio' },
                                { label: 'Logos / Patrocinadores', value: 'logo' },
                                { label: 'Programas', value: 'programa' },
                                { label: 'Eventos', value: 'evento' },
                            ]}
                            onChange={(val) => setAttributes({ postTypeOrigin: val })}
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
                        <strong>Origen de Datos:</strong> {postTypeOrigin} <br/>
                        <strong>Cargará:</strong> {itemsLimit === -1 ? 'Todos' : itemsLimit} items.
                    </p>
                    <p style={{ margin: '10px 0 0', fontSize: '12px', color: '#999' }}>El carrusel (Swiper JS) se renderizará automáticamente en la vista pública.</p>
                </div>
            </div>
        );
    },
    save: () => {
        return null;
    }
});
