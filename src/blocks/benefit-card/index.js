import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, ColorPalette, BaseControl } from '@wordpress/components';
import { useState, useRef, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

/* ─── Hook AJAX (portado desde admin/options) ─── */
const AJAX_URL = window.ajaxurl || '/wp-admin/admin-ajax.php';
const NONCE = window.vpinunf_ajax_obj?.nonce || '';

function useAjaxSearch(action, delay = 450) {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const timerRef = useRef(null);
  const abortRef = useRef(null);

  useEffect(() => {
    if (query.length < 2 && action !== "vpinunf_search_icons") {
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

/* ─── IconPicker ─── */
function IconPicker({ value, onChange }) {
  const [open, setOpen] = useState(false);
  const { query, setQuery, results, loading } = useAjaxSearch("vpinunf_search_icons");

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
          <button type="button" className="vu-tag-clear" onClick={clear} title="Quitar ícono">✕</button>
          <button type="button" className="vu-tag-change" onClick={() => setOpen((o) => !o)} title="Cambiar ícono">
            <span className="dashicons dashicons-edit" />
          </button>
        </div>
      ) : (
        <div className="vu-search-input-wrapper">
          <span className="dashicons dashicons-search vu-search-icon" />
          <input type="text" className="vu-search-input" placeholder="Buscar ícono..." value={query} onChange={(e) => { setQuery(e.target.value); setOpen(true); }} onFocus={() => setOpen(true)} autoComplete="off" />
        </div>
      )}
      {open && value && (
        <div style={{ marginTop: "8px" }}>
          <div className="vu-search-input-wrapper">
            <span className="dashicons dashicons-search vu-search-icon" />
            <input type="text" className="vu-search-input" placeholder="Buscar otro ícono..." value={query} onChange={(e) => setQuery(e.target.value)} autoComplete="off" autoFocus />
          </div>
        </div>
      )}
      {open && (
        <div className="vu-search-dropdown vu-icon-dropdown">
          {loading && <div className="vu-search-spinner"><span className="vu-spinner" /></div>}
          {!loading && query.length < 2 && <p className="vu-search-hint">Mínimo 2 letras...</p>}
          {!loading && query.length >= 2 && results.length === 0 && <p className="vu-search-empty">No encontrado.</p>}
          {!loading && results.length > 0 && (
            <ul className="vu-results-list vu-icon-results-list">
              {results.map((item) => (
                <li key={item.id} className={`vu-result-item vu-icon-result-item ${item.id === value ? "is-selected" : ""}`} onMouseDown={() => select(item.id)} title={item.type || item.title}>
                  <span className="vu-icon-result-preview"><i className={item.id} /></span>
                  <span className="vu-icon-result-label">{item.type || item.title}</span>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
}

function Edit( { attributes, setAttributes } ) {
	const { icon, iconColor, title, description, gradientStart, gradientEnd } = attributes;

	const blockProps = useBlockProps( {
		className: 'vpinunf-benefit-card',
		style: {
			background: `linear-gradient(135deg, ${ gradientStart } 0%, ${ gradientEnd } 100%)`,
		},
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configuración Visual', 'vpinunf' ) }>
					<BaseControl label={ __( 'Selección de Ícono', 'vpinunf' ) }>
                        <IconPicker value={icon} onChange={ ( val ) => setAttributes( { icon: val } ) } />
                    </BaseControl>
					<p style={ { fontSize: '12px', marginBottom: '8px', marginTop: '16px' } }>{ __( 'Color del Ícono', 'vpinunf' ) }</p>
					<ColorPalette
						value={ iconColor }
						onChange={ ( val ) => setAttributes( { iconColor: val } ) }
					/>
					<p style={ { fontSize: '13px', color: '#666', marginTop: '16px' } }>
						{ __( 'Nota: Edita el Título y la Descripción haciendo clic directamente en la tarjeta (WYSIWYG).', 'vpinunf' ) }
					</p>
				</PanelBody>
				<PanelBody title={ __( 'Color de Fondo (Degradado)', 'vpinunf' ) } initialOpen={ false }>
					<p style={ { fontSize: '12px', marginBottom: '8px' } }>{ __( 'Color inicio', 'vpinunf' ) }</p>
					<ColorPalette
						value={ gradientStart }
						onChange={ ( val ) => setAttributes( { gradientStart: val } ) }
					/>
					<p style={ { fontSize: '12px', marginBottom: '8px', marginTop: '16px' } }>{ __( 'Color fin', 'vpinunf' ) }</p>
					<ColorPalette
						value={ gradientEnd }
						onChange={ ( val ) => setAttributes( { gradientEnd: val } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="vpinunf-benefit-card__icon-wrap">
					<i className={ icon } style={{ color: iconColor }} aria-hidden="true"></i>
				</div>
				<RichText
					tagName="h3"
					className="vpinunf-benefit-card__title"
					value={ title }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					onChange={ ( val ) => setAttributes( { title: val } ) }
					placeholder={ __( 'Título del beneficio', 'vpinunf' ) }
				/>
				<RichText
					tagName="p"
					className="vpinunf-benefit-card__desc"
					value={ description }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					onChange={ ( val ) => setAttributes( { description: val } ) }
					placeholder={ __( 'Descripción corta del beneficio.', 'vpinunf' ) }
				/>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
