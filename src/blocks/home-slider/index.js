import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const blockProps = useBlockProps();

        return (
            <div {...blockProps}>
                <InspectorControls>
                    <PanelBody title="Configuración del Slider">
                        <TextControl
                            label="Autoplay Delay (ms)"
                            type="number"
                            value={attributes.autoplayDelay}
                            onChange={(val) => setAttributes({ autoplayDelay: parseInt(val) || 5000 })}
                        />
                        <SelectControl
                            label="Efecto de Transición"
                            value={attributes.effect}
                            options={[
                                { label: 'Fade', value: 'fade' },
                                { label: 'Slide', value: 'slide' },
                            ]}
                            onChange={(val) => setAttributes({ effect: val })}
                        />
                        <ToggleControl
                            label="Activar Loop"
                            checked={attributes.loop}
                            onChange={(val) => setAttributes({ loop: val })}
                        />
                        <ToggleControl
                            label="Mostrar Navegación (Flechas)"
                            checked={attributes.showNavigation}
                            onChange={(val) => setAttributes({ showNavigation: val })}
                        />
                        <ToggleControl
                            label="Mostrar Paginación (Puntos)"
                            checked={attributes.showPagination}
                            onChange={(val) => setAttributes({ showPagination: val })}
                        />
                    </PanelBody>
                    <PanelBody title="Gestión de Slides">
                        <p style={{ color: '#666', fontStyle: 'italic' }}>
                            Los slides se gestionan desde el panel lateral de WordPress, en el menú <strong>Sliders</strong>.
                        </p>
                    </PanelBody>
                </InspectorControls>

                <ServerSideRender
                    block={metadata.name}
                    attributes={attributes}
                    EmptyResponsePlaceholder={() => (
                        <div style={{ padding: '60px', textAlign: 'center', border: '2px dashed #e05e00', borderRadius: '8px', color: '#e05e00' }}>
                            No hay slides configurados o no se pudo cargar la previsualización. Agrega sliders desde el menú "Sliders".
                        </div>
                    )}
                />
            </div>
        );
    },
    save: () => null, // Dynamic block
});
