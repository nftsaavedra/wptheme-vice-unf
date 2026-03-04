import { useState, useEffect } from '@wordpress/element';
import {
    Panel, PanelBody,
    ToggleControl, RangeControl, Notice, Spinner,
    Button, TabPanel, SelectControl,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { PageSearch }    from './components/PageSearch.js';
import { IconPicker }    from './components/IconPicker.js';
import { ImageUploader } from './components/ImageUploader.js';

// ─── Primitivos UI ────────────────────────────────────────────────────────────
const FieldGroup = ({ label, help, children }) => (
    <div className="vu-field-group">
        {label && <label className="vu-field-label">{label}</label>}
        {children}
        {help && <p className="vu-field-help">{help}</p>}
    </div>
);

const Divider = () => <hr className="vu-divider" />;
const SectionNote = ({ children }) => <p className="vu-section-hint">{children}</p>;

const Input = ({ value, onChange, placeholder = '', type = 'text' }) => (
    <input className="vu-text-input" type={type} value={value}
        onChange={(e) => onChange(e.target.value)} placeholder={placeholder} />
);

const Textarea = ({ value, onChange, rows = 3, placeholder = '' }) => (
    <textarea className="vu-textarea" rows={rows} value={value}
        onChange={(e) => onChange(e.target.value)} placeholder={placeholder} />
);

// ─── InvestigacionCard ────────────────────────────────────────────────────────
function InvestigacionCard({ index, options, setOptions }) {
    const n = index + 1;
    const set = (key, val) => setOptions(prev => ({ ...prev, [key]: val }));

    return (
        <div className="vu-card">
            <div className="vu-card__badge">Ítem {n}</div>
            <FieldGroup label="Ícono">
                <IconPicker value={options[`item_${n}_icon`] || ''} onChange={(v) => set(`item_${n}_icon`, v)} />
            </FieldGroup>
            <FieldGroup label="Página">
                <PageSearch
                    value={options[`item_${n}_page_id`] || 0}
                    valueTitle={options[`item_${n}_page_title`] || ''}
                    onChange={({ id, title }) => { set(`item_${n}_page_id`, id); set(`item_${n}_page_title`, title); }}
                />
            </FieldGroup>
            <FieldGroup label="Título personalizado" help="Opcional — usa el título de la página si está vacío.">
                <Input value={options[`item_${n}_custom_title`] || ''} onChange={(v) => set(`item_${n}_custom_title`, v)} placeholder="Dejar vacío para usar el de la página" />
            </FieldGroup>
            <FieldGroup label="Descripción" help="Opcional — se genera un extracto automático si está vacío.">
                <Textarea rows={2} value={options[`item_${n}_custom_desc`] || ''} onChange={(v) => set(`item_${n}_custom_desc`, v)} />
            </FieldGroup>
        </div>
    );
}

// ─── AboutItemCard ────────────────────────────────────────────────────────────
function AboutItemCard({ index, item, onChange, onRemove }) {
    return (
        <div className="vu-card vu-card--compact">
            <div className="vu-card__header">
                <span className="vu-card__badge">Ítem {index + 1}</span>
                <button type="button" className="vu-remove-btn" onClick={onRemove} title="Eliminar">✕</button>
            </div>
            <FieldGroup label="Ícono">
                <IconPicker value={item.icon || ''} onChange={(v) => onChange({ ...item, icon: v })} />
            </FieldGroup>
            <FieldGroup label="Página">
                <PageSearch
                    value={item.page_id || 0}
                    valueTitle={item.page_title || ''}
                    onChange={({ id, title }) => onChange({ ...item, page_id: id, page_title: title })}
                />
            </FieldGroup>
        </div>
    );
}

// ─── ProductionItemCard ──────────────────────────────────────────────────────
function ProductionItemCard({ index, item, onChange, onRemove }) {
    return (
        <div className="vu-card vu-card--compact">
            <div className="vu-card__header">
                <span className="vu-card__badge">Ítem {index + 1}</span>
                <button type="button" className="vu-remove-btn" onClick={onRemove} title="Eliminar">✕</button>
            </div>
            <FieldGroup label="Título">
                <Input value={item.title || ''} onChange={(v) => onChange({ ...item, title: v })} placeholder="Ej: Revistas Científicas" />
            </FieldGroup>
            <FieldGroup label="Descripción">
                <Textarea rows={2} value={item.description || ''} onChange={(v) => onChange({ ...item, description: v })} placeholder="Breve descripción..." />
            </FieldGroup>
            <FieldGroup label="Ícono (FontAwesome clases, ej. fas fa-book)">
                <Input value={item.icon || ''} onChange={(v) => onChange({ ...item, icon: v })} placeholder="fas fa-book-open" />
            </FieldGroup>
            <FieldGroup label="Enlace (URL)">
                <Input type="url" value={item.url || ''} onChange={(v) => onChange({ ...item, url: v })} placeholder="https://..." />
            </FieldGroup>
            <ImageUploader
                label="Imagen de fondo"
                imageId={item.image_id || 0}
                imageUrl={item.image_url || ''}
                onChange={({ id, url }) => onChange({ ...item, image_id: id, image_url: url })}
            />
        </div>
    );
}

// ─── Tab: Inicio ─────────────────────────────────────────────────────────────
function TabInicio({ options, setOptions, postTypes }) {
    const set = (key, val) => setOptions(prev => ({ ...prev, [key]: val }));
    const aboutItems = Array.isArray(options.about_items) ? options.about_items : [];
    const productionItems = Array.isArray(options.production_items) ? options.production_items : [];

    // Opciones para el selector de CPT de Socios
    const postTypeOptions = [
        { label: '— Seleccionar tipo —', value: '' },
        ...postTypes.map(pt => ({ label: pt.name, value: pt.slug })),
    ];

    return (
        <Panel className="vu-panel">

            {/* ══ 1. INVESTIGACIÓN ══ */}
            <PanelBody title="① Investigación" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.investigacion_section_enabled}
                    onChange={(v) => set('investigacion_section_enabled', v ? 1 : 0)}
                />
                {!!options.investigacion_section_enabled && (
                    <>
                        <SectionNote>Configura los 4 items que aparecen en la sección. Cada uno apunta a una página y muestra su ícono, título y descripción.</SectionNote>
                        <div className="vu-card-grid vu-card-grid--2col">
                            {[0, 1, 2, 3].map((i) => (
                                <InvestigacionCard key={i} index={i} options={options} setOptions={setOptions} />
                            ))}
                        </div>
                    </>
                )}
            </PanelBody>

            {/* ══ 2. SOBRE NOSOTROS ══ */}
            <PanelBody title="② Sobre Nosotros" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.about_section_enabled}
                    onChange={(v) => set('about_section_enabled', v ? 1 : 0)}
                />
                {!!options.about_section_enabled && (
                    <>
                        <div className="vu-card vu-card--media">
                            <div className="vu-two-col">
                                <ImageUploader
                                    label="Imagen principal"
                                    imageId={options.about_main_image || 0}
                                    imageUrl={options.about_main_image_url || ''}
                                    onChange={({ id, url }) => { set('about_main_image', id); set('about_main_image_url', url); }}
                                />
                                <FieldGroup label="URL del Video (botón play)" help="YouTube, Vimeo o URL directa.">
                                    <Input type="url" value={options.about_video_url || ''} onChange={(v) => set('about_video_url', v)} placeholder="https://youtube.com/watch?v=..." />
                                </FieldGroup>
                            </div>
                        </div>
                        <Divider />
                        <div className="vu-two-col">
                            <FieldGroup label="Subtítulo animado" help="Aparece letra por letra encima del título.">
                                <Input value={options.about_subtitle || ''} onChange={(v) => set('about_subtitle', v)} placeholder="Sobre Nosotros" />
                            </FieldGroup>
                            <FieldGroup label="Título (acepta &lt;span&gt; para resaltar)">
                                <Input value={options.about_title || ''} onChange={(v) => set('about_title', v)} placeholder="Quiénes <span>Somos</span>" />
                            </FieldGroup>
                        </div>
                        <div className="vu-two-col">
                            <FieldGroup label="Nombre destacado" help="Aparece como línea secundaria dentro del h2.">
                                <Input value={options.about_person_name || ''} onChange={(v) => set('about_person_name', v)} />
                            </FieldGroup>
                            <FieldGroup label="Descripción">
                                <Textarea rows={4} value={options.about_description || ''} onChange={(v) => set('about_description', v)} />
                            </FieldGroup>
                        </div>
                        <Divider />
                        <h3 className="vu-sub-title">Items de acceso rápido</h3>
                        <SectionNote>Cards con ícono + enlace a página. Aparecen en la columna derecha de la sección.</SectionNote>
                        <div className="vu-card-grid vu-card-grid--3col">
                            {aboutItems.map((item, index) => (
                                <AboutItemCard
                                    key={index} index={index} item={item}
                                    onChange={(updated) => { const a = [...aboutItems]; a[index] = updated; set('about_items', a); }}
                                    onRemove={() => { const a = [...aboutItems]; a.splice(index, 1); set('about_items', a); }}
                                />
                            ))}
                            <div
                                className="vu-card vu-card--add"
                                onClick={() => set('about_items', [...aboutItems, { page_id: 0, page_title: '', icon: '' }])}
                                role="button" tabIndex={0}
                                onKeyDown={(e) => e.key === 'Enter' && set('about_items', [...aboutItems, { page_id: 0, page_title: '', icon: '' }])}
                            >
                                <span className="dashicons dashicons-plus-alt" />
                                <span>Añadir ítem</span>
                            </div>
                        </div>
                    </>
                )}
            </PanelBody>

            {/* ══ 3. EVENTOS ══ */}
            <PanelBody title="③ Eventos" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.eventos_section_enabled}
                    onChange={(v) => set('eventos_section_enabled', v ? 1 : 0)}
                />
                {!!options.eventos_section_enabled && (
                    <div className="vu-card vu-card--flat">
                        <div className="vu-two-col">
                            <FieldGroup label="Subtítulo">
                                <Input value={options.eventos_subtitulo || ''} onChange={(v) => set('eventos_subtitulo', v)} placeholder="Actividad Institucional" />
                            </FieldGroup>
                            <FieldGroup label="Título (acepta &lt;span&gt;)">
                                <Input value={options.eventos_titulo || ''} onChange={(v) => set('eventos_titulo', v)} placeholder="Nuestros <span>Eventos</span>" />
                            </FieldGroup>
                        </div>
                        <FieldGroup label="Descripción">
                            <Textarea rows={3} value={options.eventos_descripcion || ''} onChange={(v) => set('eventos_descripcion', v)} />
                        </FieldGroup>
                        <RangeControl
                            label="Cantidad de eventos a mostrar"
                            value={options.eventos_cantidad || 4}
                            onChange={(v) => set('eventos_cantidad', v)}
                            min={1} max={12}
                            help="Número de eventos recientes visibles en la página de inicio."
                        />
                    </div>
                )}
            </PanelBody>

            {/* ══ 4. NOTICIAS ══ */}
            <PanelBody title="④ Noticias" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.viceunf_noticias_section_enabled}
                    onChange={(v) => set('viceunf_noticias_section_enabled', v ? 1 : 0)}
                />
                {!!options.viceunf_noticias_section_enabled && (
                    <div className="vu-card vu-card--flat">
                        <div className="vu-two-col">
                            <FieldGroup label="Subtítulo">
                                <Input value={options.viceunf_noticias_subtitulo || ''} onChange={(v) => set('viceunf_noticias_subtitulo', v)} placeholder="Actualidad Académica" />
                            </FieldGroup>
                            <FieldGroup label="Título">
                                <Input value={options.viceunf_noticias_titulo || ''} onChange={(v) => set('viceunf_noticias_titulo', v)} placeholder="Últimas Noticias" />
                            </FieldGroup>
                        </div>
                        <FieldGroup label="Descripción">
                            <Textarea rows={3} value={options.viceunf_noticias_descripcion || ''} onChange={(v) => set('viceunf_noticias_descripcion', v)} />
                        </FieldGroup>
                        <RangeControl
                            label="Cantidad de noticias a mostrar"
                            value={options.noticias_cantidad || 3}
                            onChange={(v) => set('noticias_cantidad', v)}
                            min={1} max={10}
                        />
                    </div>
                )}
            </PanelBody>

            {/* ══ 5. SOCIOS ACADÉMICOS ══ */}
            <PanelBody title="⑤ Socios Académicos" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.socios_section_enabled}
                    onChange={(v) => set('socios_section_enabled', v ? 1 : 0)}
                />
                {!!options.socios_section_enabled && (
                    <div className="vu-card vu-card--flat">
                        <FieldGroup label="Título de la sección">
                            <Input value={options.viceunf_socios_titulo || ''} onChange={(v) => set('viceunf_socios_titulo', v)} placeholder="Socios Académicos" />
                        </FieldGroup>

                        <FieldGroup
                            label="Custom Post Type a mostrar"
                            help="Por defecto se usa el CPT 'socio'. Puedes cambiarlo a cualquier otro tipo de post registrado."
                        >
                            <SelectControl
                                value={options.socios_post_type || 'socio'}
                                options={postTypeOptions}
                                onChange={(v) => set('socios_post_type', v || 'socio')}
                            />
                        </FieldGroup>

                        <Notice status="info" isDismissible={false}>
                            Los logos y enlaces se gestionan desde el CPT seleccionado arriba
                            ({' '}<strong>{options.socios_post_type || 'socio'}</strong>).
                            Todos los posts publicados de ese tipo aparecen automáticamente en esta sección.
                        </Notice>
                    </div>
                )}
            </PanelBody>

            {/* ══ 6. PRODUCCIÓN CIENTÍFICA ══ */}
            <PanelBody title="⑥ Producción Científica" initialOpen={false}>
                <ToggleControl
                    label="Mostrar sección en la página de inicio"
                    checked={!!options.production_section_enabled}
                    onChange={(v) => set('production_section_enabled', v ? 1 : 0)}
                />
                {!!options.production_section_enabled && (
                    <>
                        <div className="vu-card vu-card--flat">
                            <div className="vu-two-col">
                                <FieldGroup label="Subtítulo animado" help="Aparece letra por letra">
                                    <Input value={options.production_subtitle || ''} onChange={(v) => set('production_subtitle', v)} placeholder="Publicaciones Académicas" />
                                </FieldGroup>
                                <FieldGroup label="Título (acepta <br> y <span>)">
                                    <Input value={options.production_title || ''} onChange={(v) => set('production_title', v)} placeholder="Conocimiento generado..." />
                                </FieldGroup>
                            </div>
                            <FieldGroup label="Descripción">
                                <Textarea rows={3} value={options.production_description || ''} onChange={(v) => set('production_description', v)} />
                            </FieldGroup>
                        </div>
                        <Divider />
                        <h3 className="vu-sub-title">Items de Producción</h3>
                        <SectionNote>Agrega las tarjetas que aparecerán en la grilla inferior (ej. Revistas, Libros, Boletines...)</SectionNote>
                        <div className="vu-card-grid vu-card-grid--2col">
                            {productionItems.map((item, index) => (
                                <ProductionItemCard
                                    key={index} index={index} item={item}
                                    onChange={(updated) => { const a = [...productionItems]; a[index] = updated; set('production_items', a); }}
                                    onRemove={() => { const a = [...productionItems]; a.splice(index, 1); set('production_items', a); }}
                                />
                            ))}
                            <div
                                className="vu-card vu-card--add"
                                onClick={() => set('production_items', [...productionItems, { title: '', description: '', icon: '', url: '', image_id: 0, image_url: '' }])}
                                role="button" tabIndex={0}
                                onKeyDown={(e) => e.key === 'Enter' && set('production_items', [...productionItems, { title: '', description: '', icon: '', url: '', image_id: 0, image_url: '' }])}
                            >
                                <span className="dashicons dashicons-plus-alt" />
                                <span>Añadir ítem de Producción</span>
                            </div>
                        </div>
                    </>
                )}
            </PanelBody>

        </Panel>
    );
}

// ─── Tab: Global ─────────────────────────────────────────────────────────────
function TabGlobal({ options, setOptions, postTypes }) {
    const set = (key, val) => setOptions(prev => ({ ...prev, [key]: val }));

    const postTypeOptions = postTypes.map(pt => ({ label: pt.name, value: pt.slug }));

    return (
        <Panel className="vu-panel">
            <PanelBody title="Configuración de Plantillas" initialOpen={true}>
                <div className="vu-card vu-card--flat">
                    <FieldGroup 
                        label="Plantilla de Documentos Premium (single-documento.php)" 
                        help="Selecciona los Custom Post Types que usarán el diseño moderno de documentos con vista previa PDF interactiva y tarjetas relacionadas. Múltiples post types soportados (Ctrl/Cmd + Clic para seleccionar varios)."
                    >
                        <SelectControl
                            multiple={true}
                            value={options.single_document_post_types || ['reglamento']}
                            options={postTypeOptions}
                            onChange={(values) => set('single_document_post_types', values)}
                            style={{ height: '140px' }}
                        />
                    </FieldGroup>
                </div>
            </PanelBody>
        </Panel>
    );
}

// ─── Tab: Próximamente (placeholder) ─────────────────────────────────────────
function TabProximamente({ title }) {
    return (
        <div className="vu-empty-tab">
            <span className="dashicons dashicons-hammer" />
            <h3>{title}</h3>
            <p>Esta sección estará disponible próximamente.</p>
        </div>
    );
}

// ─── App (root) ───────────────────────────────────────────────────────────────
export default function App() {
    const [options,   setOptions]   = useState(null);
    const [postTypes, setPostTypes] = useState([]);
    const [isSaving,  setIsSaving]  = useState(false);
    const [notices,   setNotices]   = useState([]);

    // Cargar opciones
    useEffect(() => {
        apiFetch({ path: '/viceunf/v1/options' })
            .then((data) => setOptions(data || {}))
            .catch(() => addNotice('No se pudo conectar con la REST API.', 'error'));
    }, []);

    // Cargar post types públicos registrados
    useEffect(() => {
        apiFetch({ path: '/wp/v2/types?per_page=100' })
            .then((data) => {
                // data es un objeto { slug: { name, slug, ... }, ... }
                const types = Object.values(data)
                    .filter(pt => {
                        const internalTypes = [
                            'attachment', 'nav_menu_item', 'revision', 'custom_css', 
                            'customize_changeset', 'oembed_cache', 'user_request', 
                            'wp_block', 'wp_template', 'wp_template_part', 
                            'wp_global_styles', 'wp_navigation', 'wp_font_family', 'wp_font_face'
                        ];
                        // Solo permitimos tipos que no están en la lista negra y que no empiezan con 'wp_'
                        return !internalTypes.includes(pt.slug) && !pt.slug.startsWith('wp_');
                    })
                    .map(pt => ({ name: pt.name, slug: pt.slug }));
                setPostTypes(types);
            })
            .catch(() => {
                // Si falla, mostramos al menos el tipo por defecto
                setPostTypes([{ name: 'Socios', slug: 'socio' }]);
            });
    }, []);

    const addNotice = (content, type = 'success') =>
        setNotices(prev => [...prev, { id: Date.now(), content, type }]);

    const handleSave = async () => {
        setIsSaving(true);
        try {
            const res = await apiFetch({ path: '/viceunf/v1/options', method: 'POST', data: options });
            addNotice(res.message || '✅ Cambios guardados correctamente.');
        } catch {
            addNotice('❌ Error al guardar las opciones.', 'error');
        } finally {
            setIsSaving(false);
        }
    };

    if (!options) {
        return (
            <div className="vu-loading">
                <Spinner />
                <span>Cargando opciones del tema...</span>
            </div>
        );
    }

    const tabs = [
        {
            name:  'inicio',
            title: '🏠 Inicio',
            className: 'vu-tab',
        },
        { 
            name: 'global', 
            title: '⚙️ Global', 
            className: 'vu-tab' 
        },
    ];

    return (
        <div className="vu-options-app">

            {/* ── Top Bar ── */}
            <div className="vu-topbar">
                <div className="vu-topbar__brand">
                    <span className="dashicons dashicons-welcome-learn-more vu-topbar__logo" />
                    <div>
                        <h2 className="vu-topbar__title">Opciones del Tema VPIN</h2>
                        <p className="vu-topbar__sub">Configura las secciones de la página de inicio y opciones globales del tema.</p>
                    </div>
                </div>
                <Button variant="primary" onClick={handleSave} isBusy={isSaving} disabled={isSaving}>
                    {isSaving ? 'Guardando…' : '💾 Guardar Cambios'}
                </Button>
            </div>

            {/* ── Tab Navigation ── */}
            <TabPanel
                className="vu-tab-panel"
                activeClass="vu-tab--active"
                tabs={tabs}
            >
                {(tab) => {
                    switch (tab.name) {
                        case 'inicio':
                            return <TabInicio options={options} setOptions={setOptions} postTypes={postTypes} />;
                        case 'global':
                            return <TabGlobal options={options} setOptions={setOptions} postTypes={postTypes} />;
                        default:
                            return <TabProximamente title={tab.title} />;
                    }
                }}
            </TabPanel>

            {/* ── Notificaciones flotantes ── */}
            <div className="vu-notices">
                {notices.map((n) => (
                    <Notice key={n.id} status={n.type} isDismissible onRemove={() => setNotices(ns => ns.filter(x => x.id !== n.id))}>
                        {n.content}
                    </Notice>
                ))}
            </div>
        </div>
    );
}
