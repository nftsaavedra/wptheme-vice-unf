import { useState, useRef } from '@wordpress/element';
import { useAjaxSearch } from '../hooks/useAjaxSearch.js';

/**
 * PageSearch
 * Busca páginas de WordPress vía AJAX y muestra un selector con tag de selección.
 *
 * Props:
 *  - value       {number}   ID de la página seleccionada
 *  - valueTitle  {string}   Título actual (para mostrar)
 *  - onChange    {fn}       ({id, title}) =>
 *  - placeholder {string}
 */
export function PageSearch({ value, valueTitle, onChange, placeholder = 'Escribe para buscar una página...' }) {
    const [open, setOpen] = useState(false);
    const { query, setQuery, results, loading } = useAjaxSearch('viceunf_search_pages_only');
    const inputRef = useRef(null);

    const select = (item) => {
        onChange({ id: item.id, title: item.title });
        setQuery('');
        setOpen(false);
    };

    const clear = () => {
        onChange({ id: 0, title: '' });
        setQuery('');
    };

    return (
        <div className="vu-search-field" style={{ position: 'relative' }}>
            {value ? (
                /* Vista: seleccionado */
                <div className="vu-selected-tag">
                    <span className="vu-tag-label">
                        <span className="vu-tag-icon dashicons dashicons-admin-page" />
                        {valueTitle || `Página #${value}`}
                    </span>
                    <button type="button" className="vu-tag-clear" onClick={clear} title="Quitar selección">✕</button>
                </div>
            ) : (
                /* Vista: input de búsqueda */
                <div className="vu-search-input-wrapper">
                    <span className="dashicons dashicons-search vu-search-icon" />
                    <input
                        ref={inputRef}
                        type="text"
                        className="vu-search-input"
                        placeholder={placeholder}
                        value={query}
                        onChange={(e) => { setQuery(e.target.value); setOpen(true); }}
                        onFocus={() => setOpen(true)}
                        autoComplete="off"
                    />
                </div>
            )}

            {/* Dropdown de resultados */}
            {open && !value && (
                <div className="vu-search-dropdown">
                    {loading && <div className="vu-search-spinner"><span className="vu-spinner" /></div>}
                    {!loading && results.length === 0 && query.length >= 2 && (
                        <p className="vu-search-empty">No se encontraron páginas.</p>
                    )}
                    {!loading && query.length < 2 && (
                        <p className="vu-search-hint">Escribe al menos 2 caracteres para buscar.</p>
                    )}
                    {!loading && results.length > 0 && (
                        <ul className="vu-results-list">
                            {results.map((item) => (
                                <li key={item.id} className="vu-result-item" onMouseDown={() => select(item)}>
                                    <span className="dashicons dashicons-admin-page vu-result-icon--page" />
                                    <span className="vu-result-title">{item.title}</span>
                                    {item.type && <span className="vu-result-type">{item.type}</span>}
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            )}
        </div>
    );
}
