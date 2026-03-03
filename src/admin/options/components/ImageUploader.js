import { useState, useEffect, useRef } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * ImageUploader
 * ─────────────────────────────────────────────────────────────
 * Usa window.wp.media nativo (no @wordpress/block-editor).
 * Evita el problema de permisos fuera del contexto del block editor.
 *
 * Resiliencia de preview:
 * Si llega un `imageId` pero `imageUrl` está vacío (datos legados),
 * resuelve la URL automáticamente desde la REST API de medios de WordPress.
 *
 * Props:
 *  - label     {string}
 *  - imageId   {number}
 *  - imageUrl  {string}
 *  - onChange  {fn} ({id, url}) =>
 */
export function ImageUploader({ label, imageId, imageUrl, onChange }) {
    const frameRef = useRef(null);

    // ─── Resolución automática de URL si hay ID pero no URL ──────────────────
    useEffect(() => {
        if (imageId && imageId > 0 && !imageUrl) {
            apiFetch({ path: `/wp/v2/media/${imageId}` })
                .then((media) => {
                    const resolvedUrl =
                        media?.media_details?.sizes?.medium?.source_url ||
                        media?.source_url ||
                        '';
                    if (resolvedUrl) {
                        // Notificamos hacia arriba sin cambiar el ID
                        onChange({ id: imageId, url: resolvedUrl });
                    }
                })
                .catch(() => {
                    // Silencioso — la preview simplemente no se muestra si la imagen no existe
                });
        }
    // Solo ejecutar cuando cambia imageId y todavía no tenemos URL
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [imageId]);
    // ─────────────────────────────────────────────────────────────────────────

    const openMediaLibrary = () => {
        if (!frameRef.current) {
            frameRef.current = window.wp.media({
                title:    'Seleccionar imagen',
                button:   { text: 'Usar esta imagen' },
                multiple: false,
                library:  { type: 'image' },
            });

            frameRef.current.on('select', () => {
                const attachment = frameRef.current.state().get('selection').first().toJSON();
                const url =
                    attachment.sizes?.medium?.url ||
                    attachment.sizes?.full?.url   ||
                    attachment.url;
                onChange({ id: attachment.id, url });
            });
        }

        if (imageId) {
            frameRef.current.on('open', () => {
                const selection  = frameRef.current.state().get('selection');
                const attachment = window.wp.media.attachment(imageId);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });
        }

        frameRef.current.open();
    };

    const remove = (e) => {
        e.stopPropagation();
        onChange({ id: 0, url: '' });
    };

    return (
        <div className="vu-field-group">
            {label && <label className="vu-field-label">{label}</label>}
            <div className="vu-image-uploader">

                {/* Vista previa clicable */}
                <div
                    className="vu-image-preview"
                    onClick={openMediaLibrary}
                    role="button"
                    tabIndex={0}
                    onKeyDown={(e) => e.key === 'Enter' && openMediaLibrary()}
                    title={imageUrl ? 'Clic para cambiar imagen' : 'Clic para seleccionar imagen'}
                >
                    {imageUrl ? (
                        <img src={imageUrl} alt="Vista previa" />
                    ) : imageId && imageId > 0 ? (
                        /* ID existe pero URL aún no se resolvió — spinner de carga */
                        <span className="vu-image-placeholder">
                            <span className="vu-spinner" />
                            <small>Cargando…</small>
                        </span>
                    ) : (
                        /* Sin imagen seleccionada */
                        <span className="vu-image-placeholder">
                            <span className="dashicons dashicons-format-image" />
                            <small>Clic para seleccionar</small>
                        </span>
                    )}
                </div>

                {/* Acciones */}
                <div className="vu-image-actions">
                    <button type="button" className="button" onClick={openMediaLibrary}>
                        {imageUrl ? 'Cambiar imagen' : 'Seleccionar imagen'}
                    </button>
                    {imageUrl && (
                        <button type="button" className="button button-link-delete" onClick={remove}>
                            Quitar
                        </button>
                    )}
                    {imageId > 0 && (
                        <p className="vu-field-help" style={{ margin: 0 }}>
                            ID: <code>{imageId}</code>
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
}
