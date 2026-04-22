import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaPlaceholder, BlockControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { images } = attributes;
    const blockProps = useBlockProps({
        className: 'vpinunf-event-gallery-editor'
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
                        title: __('Galería de Eventos Institucionales', 'vpinunf'),
                        instructions: __('Selecciona imágenes para crear una galería moderna.', 'vpinunf'),
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
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={onSelectMedia}
                            allowedTypes={['image']}
                            multiple
                            gallery
                            value={images.map(img => img.id)}
                            render={({ open }) => (
                                <ToolbarButton onClick={open} icon="edit" title={__('Editar Galería', 'vpinunf')} />
                            )}
                        />
                    </MediaUploadCheck>
                    <ToolbarButton icon="trash" title={__('Vaciar Galería', 'vpinunf')} onClick={removeImages} />
                </ToolbarGroup>
            </BlockControls>
            
            <div className="vpinunf-gallery-admin-preview" style={{ border: '1px dashed #ccc', borderRadius: '8px', padding: '16px', background: '#f9f9f9', marginTop: '16px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                    <p style={{ margin: 0, fontSize: '13px', color: '#555' }}>
                        <span className="dashicons dashicons-format-gallery" style={{ verticalAlign: 'middle', marginRight: '6px' }}></span>
                        <strong>Galería de Eventos</strong> ({images.length} imágenes)
                    </p>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={onSelectMedia}
                            allowedTypes={['image']}
                            multiple
                            gallery
                            value={images.map(img => img.id)}
                            render={({ open }) => (
                                <button onClick={open} style={{ padding: '6px 12px', background: '#fff', border: '1px solid #122a61', color: '#122a61', borderRadius: '4px', cursor: 'pointer', fontSize: '12px', fontWeight: 'bold' }}>
                                    Modificar Galería
                                </button>
                            )}
                        />
                    </MediaUploadCheck>
                </div>
                
                {/* Imagen Principal Falsa */}
                <div style={{ width: '100%', aspectRatio: '16/9', borderRadius: '8px', overflow: 'hidden', marginBottom: '16px', backgroundColor: '#000', boxShadow: '0 4px 10px rgba(0,0,0,0.1)' }}>
                    <img src={images[0].url} alt={images[0].alt} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                </div>
                
                {/* Miniaturas Falsas */}
                <div style={{ display: 'flex', gap: '12px', overflowX: 'auto', paddingBottom: '8px', scrollbarWidth: 'none' }}>
                    {images.map((img, index) => (
                        <div key={img.id} style={{ 
                            flex: '0 0 calc(25% - 9px)', 
                            aspectRatio: '16/9', 
                            borderRadius: '4px', 
                            overflow: 'hidden', 
                            opacity: index === 0 ? 1 : 0.4, 
                            filter: index === 0 ? 'grayscale(0%)' : 'grayscale(100%)',
                            transition: 'all 0.2s'
                        }}>
                            <img src={img.url} alt={img.alt} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
