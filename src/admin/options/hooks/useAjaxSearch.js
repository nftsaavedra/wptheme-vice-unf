import { useState, useRef, useEffect, useCallback } from '@wordpress/element';

const AJAX_URL = window.ajaxurl || '/wp-admin/admin-ajax.php';
const NONCE    = window.viceunf_ajax_obj?.nonce || '';

/**
 * Hook: búsqueda AJAX con debounce.
 * @param {string} action  - Acción wp_ajax_*
 * @param {number} delay   - ms de debounce (default 450)
 */
export function useAjaxSearch( action, delay = 450 ) {
    const [query, setQuery]     = useState('');
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const timerRef              = useRef(null);
    const abortRef              = useRef(null);

    useEffect(() => {
        if (query.length < 2 && action !== 'viceunf_search_icons') {
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
                fd.append('action', action);
                fd.append('nonce',  NONCE);
                fd.append('search', query);
                const res  = await fetch(AJAX_URL, { method: 'POST', body: fd, signal: abortRef.current.signal });
                const json = await res.json();
                setResults(json.success && Array.isArray(json.data) ? json.data : []);
            } catch (e) {
                if (e.name !== 'AbortError') setResults([]);
            } finally {
                setLoading(false);
            }
        }, delay);
        return () => clearTimeout(timerRef.current);
    }, [query, action, delay]);

    return { query, setQuery, results, loading };
}
