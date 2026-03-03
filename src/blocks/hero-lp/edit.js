import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	Button,
} from '@wordpress/components';

/**
 * Componente de edición para el bloque viceunf/hero-lp.
 * Vista del editor: placeholder con configuración completa via InspectorControls.
 *
 * @param {Object} props               Propiedades del bloque.
 * @param {Object} props.attributes    Atributos actuales.
 * @param {Function} props.setAttributes Función para actualizar atributos.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		backgroundImage,
		backgroundVideo,
		overlayOpacity,
		programLogo,
		title,
		subtitle,
		ctaPrimaryText,
		ctaPrimaryUrl,
		ctaSecondaryText,
		ctaSecondaryUrl,
	} = attributes;

	const hasBackground = backgroundImage?.url || backgroundVideo;

	return (
		<>
			<InspectorControls>
				{/* ── Fondo ── */}
				<PanelBody title={ __( 'Fondo', 'viceunf' ) } initialOpen={ true }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									backgroundImage: {
										url: media.url,
										id: media.id,
										alt: media.alt,
									},
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ backgroundImage?.id }
							render={ ( { open } ) => (
								<div className="viceunf-media-upload-wrap">
									{ backgroundImage?.url ? (
										<>
											<img
												src={ backgroundImage.url }
												alt={ backgroundImage.alt || '' }
												style={ { width: '100%', height: '80px', objectFit: 'cover', marginBottom: '8px', borderRadius: '4px' } }
											/>
											<Button
												variant="secondary"
												isDestructive
												onClick={ () => setAttributes( { backgroundImage: {} } ) }
												style={ { marginBottom: '8px', display: 'block', width: '100%' } }
											>
												{ __( 'Quitar imagen', 'viceunf' ) }
											</Button>
										</>
									) : null }
									<Button variant="primary" onClick={ open } style={ { width: '100%' } }>
										{ backgroundImage?.url
											? __( 'Cambiar imagen de fondo', 'viceunf' )
											: __( 'Seleccionar imagen de fondo', 'viceunf' ) }
									</Button>
								</div>
							) }
						/>
					</MediaUploadCheck>

					<TextControl
						label={ __( 'URL de video de fondo (opcional)', 'viceunf' ) }
						help={ __( 'Si se ingresa, el video tendrá prioridad sobre la imagen.', 'viceunf' ) }
						value={ backgroundVideo }
						onChange={ ( val ) => setAttributes( { backgroundVideo: val } ) }
						type="url"
					/>

					<RangeControl
						label={ __( 'Opacidad del overlay oscuro', 'viceunf' ) }
						value={ overlayOpacity }
						onChange={ ( val ) => setAttributes( { overlayOpacity: val } ) }
						min={ 0 }
						max={ 1 }
						step={ 0.05 }
					/>
				</PanelBody>

				{/* ── Logo del Programa ── */}
				<PanelBody title={ __( 'Logo del Programa', 'viceunf' ) } initialOpen={ false }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									programLogo: { url: media.url, id: media.id, alt: media.alt },
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ programLogo?.id }
							render={ ( { open } ) => (
								<div>
									{ programLogo?.url && (
										<>
											<img
												src={ programLogo.url }
												alt={ programLogo.alt || '' }
												style={ { maxHeight: '60px', marginBottom: '8px', display: 'block' } }
											/>
											<Button
												variant="secondary"
												isDestructive
												onClick={ () => setAttributes( { programLogo: {} } ) }
												style={ { marginBottom: '8px', display: 'block', width: '100%' } }
											>
												{ __( 'Quitar logo', 'viceunf' ) }
											</Button>
										</>
									) }
									<Button variant="primary" onClick={ open } style={ { width: '100%' } }>
										{ programLogo?.url
											? __( 'Cambiar logo', 'viceunf' )
											: __( 'Seleccionar logo', 'viceunf' ) }
									</Button>
								</div>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>

				{/* ── Contenido ── */}
				<PanelBody title={ __( 'Contenido', 'viceunf' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Título principal (H1)', 'viceunf' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextControl
						label={ __( 'Subtítulo / Etiqueta', 'viceunf' ) }
						value={ subtitle }
						onChange={ ( val ) => setAttributes( { subtitle: val } ) }
					/>
				</PanelBody>

				{/* ── CTAs ── */}
				<PanelBody title={ __( 'Botones CTA', 'viceunf' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Texto botón primario', 'viceunf' ) }
						value={ ctaPrimaryText }
						onChange={ ( val ) => setAttributes( { ctaPrimaryText: val } ) }
					/>
					<TextControl
						label={ __( 'URL botón primario', 'viceunf' ) }
						value={ ctaPrimaryUrl }
						onChange={ ( val ) => setAttributes( { ctaPrimaryUrl: val } ) }
						type="url"
					/>
					<TextControl
						label={ __( 'Texto botón secundario (opcional)', 'viceunf' ) }
						value={ ctaSecondaryText }
						onChange={ ( val ) => setAttributes( { ctaSecondaryText: val } ) }
					/>
					<TextControl
						label={ __( 'URL botón secundario (opcional)', 'viceunf' ) }
						value={ ctaSecondaryUrl }
						onChange={ ( val ) => setAttributes( { ctaSecondaryUrl: val } ) }
						type="url"
					/>
				</PanelBody>
			</InspectorControls>

			{/* ── Vista del editor ── */}
			<div
				className="viceunf-hero-lp-editor-preview"
				style={ {
					position: 'relative',
					minHeight: '320px',
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'center',
					flexDirection: 'column',
					backgroundColor: hasBackground ? 'transparent' : '#0e1422',
					backgroundImage: backgroundImage?.url ? `url(${ backgroundImage.url })` : 'none',
					backgroundSize: 'cover',
					backgroundPosition: 'center',
					padding: '4rem 2rem',
					borderRadius: '4px',
					overflow: 'hidden',
				} }
			>
				<div
					style={ {
						position: 'absolute',
						inset: 0,
						background: `rgba(14,20,34,${ overlayOpacity })`,
					} }
				/>
				<div style={ { position: 'relative', zIndex: 1, textAlign: 'center', maxWidth: '720px' } }>
					{ programLogo?.url && (
						<img
							src={ programLogo.url }
							alt={ programLogo.alt || '' }
							style={ { maxHeight: '80px', marginBottom: '1.6rem' } }
						/>
					) }
					{ subtitle && (
						<p style={ { color: '#ff4700', fontWeight: 700, fontSize: '1.4rem', textTransform: 'uppercase', letterSpacing: '0.15em', marginBottom: '0.8rem' } }>
							{ subtitle }
						</p>
					) }
					<h1 style={ { color: '#ffffff', fontSize: '3.6rem', fontWeight: 800, marginBottom: '1.6rem', margin: '0 0 1.6rem' } }>
						{ title || __( '[ Título del programa ]', 'viceunf' ) }
					</h1>
					<div style={ { display: 'flex', gap: '1.2rem', justifyContent: 'center', flexWrap: 'wrap' } }>
						{ ctaPrimaryText && (
							<span style={ { background: '#ff4700', color: '#fff', padding: '1.2rem 3rem', fontWeight: 700, fontSize: '1.5rem', borderRadius: '2px' } }>
								{ ctaPrimaryText }
							</span>
						) }
						{ ctaSecondaryText && (
							<span style={ { border: '2px solid #fff', color: '#fff', padding: '1.2rem 3rem', fontWeight: 700, fontSize: '1.5rem', borderRadius: '2px' } }>
								{ ctaSecondaryText }
							</span>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
