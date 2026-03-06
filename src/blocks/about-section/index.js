import './style.scss';
import { registerBlockType } from "@wordpress/blocks";
import {
  useBlockProps,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
  BaseControl,
} from "@wordpress/components";
import { useState, useRef, useEffect } from "@wordpress/element";
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
      items,
    } = attributes;

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
          {/* ── Panel: Contenido Principal ── */}
          <PanelBody title="Contenido Principal" initialOpen={true}>
            <TextControl
              label="Subtítulo"
              value={subtitle}
              onChange={(v) => setAttributes({ subtitle: v })}
            />
            <TextControl
              label="Título"
              value={title}
              onChange={(v) => setAttributes({ title: v })}
            />
            <TextControl
              label="Nombre de Persona (opcional)"
              value={personName}
              onChange={(v) => setAttributes({ personName: v })}
            />
            <TextareaControl
              label="Descripción"
              value={description}
              onChange={(v) => setAttributes({ description: v })}
              rows={4}
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

        {/* ── Preview en el Editor ── */}
        <div
          style={{
            padding: "24px",
            border: "2px dashed var(--dt-sec-color, #0b2346)",
            textAlign: "center",
            backgroundColor: "var(--viceunf-surface-alt, #f1f5f9)",
            borderRadius: "8px",
          }}
        >
          <h3 style={{ margin: 0, color: "var(--dt-sec-color, #0b2346)" }}>
            [Bloque: Sección Nosotros]
          </h3>
          <p style={{ margin: "10px 0 0", fontSize: "14px", color: "#666" }}>
            <strong>Subtítulo:</strong> {subtitle || "(vacío)"} <br />
            <strong>Título:</strong> {title || "(vacío)"} <br />
            {personName && (
              <>
                <strong>Persona:</strong> {personName} <br />
              </>
            )}
            <strong>Imagen:</strong>{" "}
            {mainImageUrl ? "✓ Configurada" : "✗ Sin imagen"} <br />
            <strong>Video:</strong> {videoUrl ? "✓ Configurado" : "✗ Sin video"}{" "}
            <br />
            <strong>Items:</strong> {items.length} configurado(s)
          </p>
          {items.length > 0 && (
            <div
              style={{
                display: "flex",
                gap: "8px",
                justifyContent: "center",
                flexWrap: "wrap",
                marginTop: "12px",
              }}
            >
              {items.map((item, i) => (
                <span
                  key={i}
                  style={{
                    padding: "4px 10px",
                    background: "var(--viceunf-surface, #fff)",
                    border: "1px solid var(--viceunf-border-color, #eaeaea)",
                    borderRadius: "4px",
                    fontSize: "12px",
                  }}
                >
                  {item.icon && (
                    <i className={item.icon} style={{ marginRight: "4px" }} />
                  )}
                  {item.title || `Item ${i + 1}`}
                </span>
              ))}
            </div>
          )}
          <p style={{ margin: "12px 0 0", fontSize: "11px", color: "#999" }}>
            El layout completo se renderiza en la vista pública.
          </p>
        </div>
      </div>
    );
  },
  save() {
    return null;
  },
});
