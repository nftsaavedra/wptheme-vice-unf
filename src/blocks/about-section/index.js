import './style.scss';
import { registerBlockType } from "@wordpress/blocks";
import {
  useBlockProps,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
  RichText,
  BlockControls,
  AlignmentControl,
} from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
  BaseControl,
} from "@wordpress/components";
import { useState, useRef, useEffect } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import metadata from "./block.json";

/* ─── Hook AJAX (portado desde admin/options) ─── */
const AJAX_URL = window.ajaxurl || "/wp-admin/admin-ajax.php";
const NONCE = window.viceunf_ajax_obj?.nonce || "";

function useAjaxSearch(action, delay = 450) {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const timerRef = useRef(null);
  const abortRef = useRef(null);

  useEffect(() => {
    if (query.length < 2 && action !== "viceunf_search_icons") {
      setResults([]);
      return;
    }
    clearTimeout(timerRef.current);
    timerRef.current = setTimeout(async () => {
      if (abortRef.current) abortRef.current.abort();
      abortRef.current = new AbortController();
      setLoading(true);
      try {
        const fd = new FormData();
        fd.append("action", action);
        fd.append("nonce", NONCE);
        fd.append("search", query);
        const res = await fetch(AJAX_URL, {
          method: "POST",
          body: fd,
          signal: abortRef.current.signal,
        });
        const json = await res.json();
        setResults(json.success && Array.isArray(json.data) ? json.data : []);
      } catch (e) {
        if (e.name !== "AbortError") setResults([]);
      } finally {
        setLoading(false);
      }
    }, delay);
    return () => clearTimeout(timerRef.current);
  }, [query, action, delay]);

  return { query, setQuery, results, loading };
}

/* ─── IconPicker (portado desde admin/options) ─── */
function IconPicker({ value, onChange }) {
  const [open, setOpen] = useState(false);
  const { query, setQuery, results, loading } = useAjaxSearch(
    "viceunf_search_icons",
  );

  const select = (iconClass) => {
    onChange(iconClass);
    setQuery("");
    setOpen(false);
  };
  const clear = () => {
    onChange("");
    setQuery("");
  };

  return (
    <div className="vu-icon-picker" style={{ position: "relative" }}>
      {value ? (
        <div className="vu-selected-tag vu-selected-tag--icon">
          <span className="vu-tag-icon-preview">
            <i className={value} aria-hidden="true" />
          </span>
          <span className="vu-tag-label" title={value}>
            {value}
          </span>
          <button
            type="button"
            className="vu-tag-clear"
            onClick={clear}
            title="Quitar ícono"
          >
            ✕
          </button>
          <button
            type="button"
            className="vu-tag-change"
            onClick={() => setOpen((o) => !o)}
            title="Cambiar ícono"
          >
            <span className="dashicons dashicons-edit" />
          </button>
        </div>
      ) : (
        <div className="vu-search-input-wrapper">
          <span className="dashicons dashicons-search vu-search-icon" />
          <input
            type="text"
            className="vu-search-input"
            placeholder="Buscar ícono Font Awesome..."
            value={query}
            onChange={(e) => {
              setQuery(e.target.value);
              setOpen(true);
            }}
            onFocus={() => setOpen(true)}
            autoComplete="off"
          />
        </div>
      )}
      {open && value && (
        <div style={{ marginTop: "8px" }}>
          <div className="vu-search-input-wrapper">
            <span className="dashicons dashicons-search vu-search-icon" />
            <input
              type="text"
              className="vu-search-input"
              placeholder="Buscar otro ícono..."
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              autoComplete="off"
              autoFocus
            />
          </div>
        </div>
      )}
      {open && (
        <div className="vu-search-dropdown vu-icon-dropdown">
          {loading && (
            <div className="vu-search-spinner">
              <span className="vu-spinner" />
            </div>
          )}
          {!loading && query.length < 2 && (
            <p className="vu-search-hint">
              Escribe al menos 2 letras (ej: flask, book).
            </p>
          )}
          {!loading && query.length >= 2 && results.length === 0 && (
            <p className="vu-search-empty">No se encontraron íconos.</p>
          )}
          {!loading && results.length > 0 && (
            <ul className="vu-results-list vu-icon-results-list">
              {results.map((item) => (
                <li
                  key={item.id}
                  className={`vu-result-item vu-icon-result-item ${item.id === value ? "is-selected" : ""}`}
                  onMouseDown={() => select(item.id)}
                  title={item.type || item.title}
                >
                  <span className="vu-icon-result-preview">
                    <i className={item.id} />
                  </span>
                  <span className="vu-icon-result-label">
                    {item.type || item.title}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
}

/* ─── AutoridadPicker ─── */
function AutoridadPicker({ value, onChange }) {
  const [open, setOpen] = useState(false);
  const { query, setQuery, results, loading } = useAjaxSearch(
    "viceunf_search_autoridades"
  );

  const currentAutoridad = useSelect((select) => {
    return value ? select("core").getEntityRecord("postType", "autoridad", value) : null;
  }, [value]);

  const selectItem = (item) => {
    onChange(item.id);
    setQuery("");
    setOpen(false);
  };
  const clear = () => {
    onChange(0);
    setQuery("");
  };

  const displayTitle = currentAutoridad ? (currentAutoridad.title?.rendered || currentAutoridad.title) : (value ? `Cargando ID: ${value}...` : "");

  return (
    <div className="vu-icon-picker" style={{ position: "relative", marginBottom: "16px" }}>
      {value ? (
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '12px', border: '1px solid var(--viceunf-primary-color, #e05e00)', borderRadius: '4px', background: 'rgba(224, 94, 0, 0.05)' }}>
          <span style={{ fontWeight: 'bold', flex: 1 }}>
            <span className="dashicons dashicons-businessman" style={{ marginRight: '8px', color: 'var(--viceunf-primary-color, #e05e00)' }}></span>
            {displayTitle}
          </span>
          <div style={{ display: 'flex', gap: '8px' }}>
            <Button variant="secondary" isSmall onClick={() => setOpen((o) => !o)} title="Cambiar Autoridad">
              <span className="dashicons dashicons-edit" />
            </Button>
            <Button isDestructive isSmall onClick={clear} title="Quitar Autoridad">
              ✕
            </Button>
          </div>
        </div>
      ) : (
        <div className="vu-search-input-wrapper">
          <span className="dashicons dashicons-search vu-search-icon" />
          <input
            type="text"
            className="vu-search-input"
            placeholder="Buscar Autoridad por nombre..."
            value={query}
            onChange={(e) => {
              setQuery(e.target.value);
              setOpen(true);
            }}
            onFocus={() => setOpen(true)}
            autoComplete="off"
            style={{ width: '100%', padding: '8px 8px 8px 30px', boxSizing: 'border-box' }}
          />
        </div>
      )}
      
      {open && value && (
        <div style={{ marginTop: "8px" }}>
          <div className="vu-search-input-wrapper">
            <span className="dashicons dashicons-search vu-search-icon" />
            <input
              type="text"
              className="vu-search-input"
              placeholder="Buscar otra autoridad..."
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              autoComplete="off"
              autoFocus
              style={{ width: '100%', padding: '8px 8px 8px 30px', boxSizing: 'border-box' }}
            />
          </div>
        </div>
      )}

      {open && (
        <div className="vu-search-dropdown vu-icon-dropdown" style={{ maxHeight: '250px', overflowY: 'auto' }}>
          {loading && (
            <div className="vu-search-spinner">
              <span className="vu-spinner" />
            </div>
          )}
          {!loading && query.length < 2 && (
            <p className="vu-search-hint">Escribe al menos 2 letras.</p>
          )}
          {!loading && query.length >= 2 && results.length === 0 && (
            <p className="vu-search-empty">No se encontraron autoridades.</p>
          )}
          {!loading && results.length > 0 && (
            <ul className="vu-results-list vu-icon-results-list">
              {results.map((item) => (
                <li
                  key={item.id}
                  className={`vu-result-item vu-icon-result-item ${item.id === value ? "is-selected" : ""}`}
                  onMouseDown={() => selectItem(item)}
                  style={{ padding: '8px', cursor: 'pointer', borderBottom: '1px solid #eee' }}
                >
                  <span className="dashicons dashicons-businessman" style={{ marginRight: '8px', color: '#666' }} />
                  <span className="vu-icon-result-label" style={{ fontWeight: item.id === value ? 'bold' : 'normal' }}>
                    {item.title}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
}

/* ─── Registro del Bloque ─── */
registerBlockType(metadata.name, {
  edit({ attributes, setAttributes }) {
    const {
      subtitle,
      title,
      personName,
      description,
      mainImageId,
      mainImageUrl,
      mainImageAlt,
      videoUrl,
      autoridadId,
      items,
    } = attributes;

    const currentAutoridadInfo = useSelect((select) => {
      return autoridadId ? select("core").getEntityRecord("postType", "autoridad", autoridadId) : null;
    }, [autoridadId]);

    const blockProps = useBlockProps({
      className: "viceunf-about-section-editor-preview",
    });

    const updateItem = (index, key, value) => {
      const updated = [...items];
      updated[index] = { ...updated[index], [key]: value };
      setAttributes({ items: updated });
    };
    const addItem = () =>
      setAttributes({ items: [...items, { title: "", url: "", icon: "" }] });
    const removeItem = (index) =>
      setAttributes({ items: items.filter((_, i) => i !== index) });

    return (
      <div {...blockProps}>
        <InspectorControls>
          {/* ── Panel: Vinculación de Autoridad ── */}
          <PanelBody title="Vinculación con Autoridad" initialOpen={true}>
            <p style={{ fontSize: '13px', color: '#666', marginBottom: '12px' }}>
              Si seleccionas una Autoridad, su <strong>Fotografía</strong>, <strong>Grado</strong> y <strong>Nombre</strong> reemplazarán automáticamente los campos manuales en la web pública.
            </p>
            <AutoridadPicker
              value={autoridadId}
              onChange={(val) => setAttributes({ autoridadId: val })}
            />
          </PanelBody>

          {/* ── Panel: Imagen Principal ── */}
          <PanelBody title="Imagen Principal" initialOpen={false}>
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(media) =>
                  setAttributes({
                    mainImageId: media.id,
                    mainImageUrl: media.url,
                    mainImageAlt: media.alt || "",
                  })
                }
                allowedTypes={["image"]}
                value={mainImageId}
                render={({ open }) => (
                  <div>
                    {mainImageUrl ? (
                      <div style={{ marginBottom: "10px" }}>
                        <img
                          src={mainImageUrl}
                          alt={mainImageAlt}
                          style={{ maxWidth: "100%", borderRadius: "4px" }}
                        />
                        <div
                          style={{
                            display: "flex",
                            gap: "8px",
                            marginTop: "8px",
                          }}
                        >
                          <Button variant="secondary" onClick={open}>
                            Cambiar Imagen
                          </Button>
                          <Button
                            isDestructive
                            onClick={() =>
                              setAttributes({
                                mainImageId: 0,
                                mainImageUrl: "",
                                mainImageAlt: "",
                              })
                            }
                          >
                            Quitar
                          </Button>
                        </div>
                      </div>
                    ) : (
                      <Button variant="secondary" onClick={open}>
                        Seleccionar Imagen
                      </Button>
                    )}
                  </div>
                )}
              />
            </MediaUploadCheck>
            <TextControl
              label="Texto Alt"
              value={mainImageAlt}
              onChange={(v) => setAttributes({ mainImageAlt: v })}
            />
          </PanelBody>

          {/* ── Panel: Video ── */}
          <PanelBody title="Video (Lightbox)" initialOpen={false}>
            <TextControl
              label="URL del Video"
              value={videoUrl}
              onChange={(v) => setAttributes({ videoUrl: v })}
              placeholder="https://www.youtube.com/watch?v=..."
            />
          </PanelBody>

          {/* ── Panel: Items de Navegación ── */}
          <PanelBody title="Items de Navegación" initialOpen={false}>
            {items.map((item, i) => (
              <div
                key={i}
                style={{
                  border: "1px solid var(--viceunf-border-color, #eaeaea)",
                  borderRadius: "4px",
                  padding: "12px",
                  marginBottom: "12px",
                }}
              >
                <BaseControl label={`Item ${i + 1}`}>
                  <TextControl
                    label="Título"
                    value={item.title || ""}
                    onChange={(v) => updateItem(i, "title", v)}
                  />
                  <TextControl
                    label="URL (enlace)"
                    value={item.url || ""}
                    onChange={(v) => updateItem(i, "url", v)}
                    placeholder="https://..."
                  />
                  <BaseControl label="Ícono">
                    <IconPicker
                      value={item.icon || ""}
                      onChange={(v) => updateItem(i, "icon", v)}
                    />
                  </BaseControl>
                  <Button
                    isDestructive
                    variant="tertiary"
                    onClick={() => removeItem(i)}
                    style={{ marginTop: "8px" }}
                  >
                    Eliminar Item
                  </Button>
                </BaseControl>
              </div>
            ))}
            <Button
              variant="secondary"
              onClick={addItem}
              style={{ width: "100%" }}
            >
              <span
                className="dashicons dashicons-plus-alt"
                style={{ marginRight: "4px" }}
              />{" "}
              Agregar Item
            </Button>
          </PanelBody>
        </InspectorControls>

        {/* ── Preview en el Editor (WYSIWYG) ── */}
        <div
          style={{
            padding: "24px",
            border: "1px solid var(--viceunf-border-color, #eaeaea)",
            backgroundColor: "var(--viceunf-surface-alt, #fcfcfc)",
            borderRadius: "8px",
          }}
        >
          <BlockControls group="block">
            <AlignmentControl
              value={attributes.textAlign}
              onChange={(val) => setAttributes({ textAlign: val })}
            />
          </BlockControls>
          
          <div style={{ textAlign: attributes.textAlign || 'left', marginBottom: '32px' }}>
            <RichText
              tagName="p"
              value={subtitle}
              onChange={(v) => setAttributes({ subtitle: v })}
              placeholder="Escribe el subtítulo..."
              style={{ color: "var(--viceunf-primary-color, #e05e00)", fontWeight: "bold", textTransform: "uppercase", fontSize: "14px", marginBottom: "8px" }}
            />
            <RichText
              tagName="h2"
              value={title}
              onChange={(v) => setAttributes({ title: v })}
              placeholder="Escribe el título de la sección..."
              style={{ color: "var(--dt-sec-color, #0b2346)", fontWeight: "900", fontSize: "32px", marginBottom: "4px" }}
            />
            
            {autoridadId ? (
              <div style={{ padding: '8px 12px', background: 'rgba(224,94,0,0.1)', display: 'inline-block', borderRadius: '4px', marginBottom: '16px', border: '1px solid rgba(224,94,0,0.3)', color: '#e05e00', fontWeight: 'bold' }}>
                <span className="dashicons dashicons-businessman" style={{ marginRight: '6px' }} />
                [ Datos de Persona Vinculados Dinámicamente ]
              </div>
            ) : (
              <RichText
                tagName="div"
                value={personName}
                onChange={(v) => setAttributes({ personName: v })}
                placeholder="Nombre de la persona (Opcional)"
                style={{ fontSize: "18px", fontWeight: "bold", color: "#555", marginBottom: "16px" }}
              />
            )}

            <RichText
              tagName="div"
              value={description}
              onChange={(v) => setAttributes({ description: v })}
              placeholder="Escribe la descripción fluida aquí..."
              style={{ color: "#666", fontSize: "16px", lineHeight: "1.6" }}
            />
          </div>

          <div style={{ padding: "12px", border: "1px dashed #ccc", marginBottom: "24px", background: "#fdfdfd" }}>
            <strong>Media Adjunta:</strong>{" "}
            {autoridadId ? "✓ Foto Dinámica (Autoridad)" : (mainImageUrl ? "✓ Imagen Configurada Manualmente" : "✗ Sin imagen principal")} | {" "}
            {videoUrl ? "✓ Video Configurado" : "✗ Sin video"}
          </div>

          {items.length > 0 && (
            <div
              style={{
                display: "grid",
                gridTemplateColumns: "1fr 1fr",
                gap: "16px",
              }}
            >
              {items.map((item, i) => (
                <div
                  key={i}
                  style={{
                    display: "flex",
                    alignItems: "center",
                    gap: "12px",
                    padding: "16px",
                    background: "var(--viceunf-surface, #fff)",
                    border: "1px solid var(--viceunf-border-color, #eaeaea)",
                    borderRadius: "8px",
                    cursor: "pointer",
                    transition: "all 0.3s",
                    boxShadow: "0 2px 4px rgba(0,0,0,0.02)"
                  }}
                >
                  {item.icon && (
                    <i className={item.icon} style={{ fontSize: "24px", color: "var(--viceunf-primary-color, #e05e00)" }} />
                  )}
                  <RichText
                    tagName="h5"
                    value={item.title}
                    onChange={(v) => updateItem(i, "title", v)}
                    placeholder={`Título del ítem ${i + 1}`}
                    style={{ margin: 0, fontSize: "18px", color: "var(--dt-sec-color, #0b2346)" }}
                  />
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    );
  },
  save() {
    return null;
  },
});
