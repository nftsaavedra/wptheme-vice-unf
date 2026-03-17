import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaPlaceholder, BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { images } = attributes;
    const blockProps = useBlockProps({
        className: 'viceunf-event-gallery-editor'
    });

    const onSelectMedia = (media) => {
        const parsedImages = media.map(img => ({
            id: img.id,
            url: img.url,
            alt: img.alt || img.title || 'Event image'
        }));
        setAttributes({ images: parsedImages });
    };

    const removeImages = () => {
        setAttributes({ images: [] });
    };

    if (!images || images.length === 0) {
        return (
            <div { ...blockProps }>
                <MediaPlaceholder
                    icon="format-gallery"
                    labels={{
                        title: __('Galería de Eventos Institucionales', 'viceunf'),
                        instructions: __('Selecciona imágenes para crear una galería moderna.', 'viceunf'),
                    }}
                    onSelect={onSelectMedia}
                    accept="image/*"
                    multiple={true}
                    gallery={true}
                />
            </div>
        );
    }

    return (
        <div { ...blockProps }>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon="trash" title={__('Vaciar Galería', 'viceunf')} onClick={removeImages} />
                </ToolbarGroup>
            </BlockControls>
            
            <div className="viceunf-gallery-admin-preview">
                <p><strong>Vista previa del Editor:</strong> {images.length} imágenes seleccionadas.</p>
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(100px, 1fr))', gap: '10px' }}>
                    {images.map(img => (
                        <div key={img.id} style={{ position: 'relative' }}>
                            <img src={img.url} alt={img.alt} style={{ width: '100%', height: '80px', objectFit: 'cover', borderRadius: '4px' }} />
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
