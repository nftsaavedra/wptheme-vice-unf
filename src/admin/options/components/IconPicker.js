import { useState, useEffect } from '@wordpress/element';
import { useAjaxSearch } from '../hooks/useAjaxSearch.js';

/**
 * IconPicker
 * Busca iconos de Font Awesome via AJAX con vista previa en tiempo real.
 *
 * Props:
 *  - value     {string}  clase actual (ej: 'fas fa-flask')
 *  - onChange  {fn}      (newClass: string) =>
 */
export function IconPicker({ value, onChange }) {
    const [open, setOpen] = useState(false);
    const [displayName, setDisplayName] = useState(value);
    const { query, setQuery, results, loading } = useAjaxSearch('viceunf_search_icons');

    // Efecto para humanizar la clase guardada cuando el componente se monta
    useEffect(() => {
        if (value) {
            fetch(`${viceunfAdminData.themeUrl}/assets/data/fontawesome-icons.json`)
                .then(res => res.json())
                .then(icons => {
                    const icon = icons.find(i => i.class === value);
                    if (icon) {
                        setDisplayName(icon.name);
                    } else {
                        // Si no lo encuentra, usa la misma clase como fallback
                        setDisplayName(value);
                    }
                })
                .catch(() => setDisplayName(value));
        }
    }, [value]);

    const select = (iconClass) => {
        onChange(iconClass);
        setQuery('');
        setOpen(false);
    };

    const clear = () => {
        onChange('');
        setQuery('');
    };

    return (
        <div className="vu-icon-picker" style={{ position: 'relative' }}>
            {value ? (
                /* Estado: icono seleccionado */
                <div className="vu-selected-tag vu-selected-tag--icon">
                    <span className="vu-tag-icon-preview">
                        <i className={value} aria-hidden="true" />
                    </span>
                    <span className="vu-tag-label" title={value}>{displayName}</span>
                    <button type="button" className="vu-tag-clear" onClick={clear} title="Quitar ícono">✕</button>
                    <button type="button" className="vu-tag-change" onClick={() => { setOpen((o) => !o); }} title="Cambiar ícono">
                        <span className="dashicons dashicons-edit" />
                    </button>
                </div>
            ) : (
                /* Estado: sin ícono */
                <div className="vu-search-input-wrapper">
                    <span className="dashicons dashicons-search vu-search-icon" />
                    <input
                        type="text"
                        className="vu-search-input"
                        placeholder="Buscar ícono Font Awesome... (ej: flask, book)"
                        value={query}
                        onChange={(e) => { setQuery(e.target.value); setOpen(true); }}
                        onFocus={() => setOpen(true)}
                        autoComplete="off"
                    />
                </div>
            )}

            {/* Si está seleccionado pero quiere cambiar */}
            {open && value && (
                <div style={{ marginTop: '8px' }}>
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

            {/* Dropdown de resultados */}
            {open && (
                <div className="vu-search-dropdown vu-icon-dropdown">
                    {loading && <div className="vu-search-spinner"><span className="vu-spinner" /></div>}
                    {!loading && query.length < 2 && (
                        <p className="vu-search-hint">Escribe para buscar (ej: microscope, book, flask).</p>
                    )}
                    {!loading && query.length >= 2 && results.length === 0 && (
                        <p className="vu-search-empty">No se encontraron íconos.</p>
                    )}
                    {!loading && results.length > 0 && (
                        <ul className="vu-results-list vu-icon-results-list">
                            {results.map((item) => (
                                <li
                                    key={item.id}
                                    className={`vu-result-item vu-icon-result-item ${item.id === value ? 'is-selected' : ''}`}
                                    onMouseDown={() => select(item.id)}
                                    title={item.type || item.title}
                                >
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
