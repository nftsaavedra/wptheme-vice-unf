import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	BlockControls,
	AlignmentControl,
	PanelColorSettings
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	Button,
} from '@wordpress/components';

/**
 * Componente de edición para el bloque vpinunf/hero-lp.
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
		subtitleColor,
		ctaPrimaryBgColor,
		ctaPrimaryTextColor,
		ctaSecondaryBorderColor,
		ctaSecondaryTextColor,
	} = attributes;

	const hasBackground = backgroundImage?.url || backgroundVideo;

	return (
		<>
			<InspectorControls>
				{/* ── Fondo ── */}
				<PanelBody title={ __( 'Fondo', 'vpinunf' ) } initialOpen={ true }>
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
								<div className="vpinunf-media-upload-wrap">
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
												{ __( 'Quitar imagen', 'vpinunf' ) }
											</Button>
										</>
									) : null }
									<Button variant="primary" onClick={ open } style={ { width: '100%' } }>
										{ backgroundImage?.url
											? __( 'Cambiar imagen de fondo', 'vpinunf' )
											: __( 'Seleccionar imagen de fondo', 'vpinunf' ) }
									</Button>
								</div>
							) }
						/>
					</MediaUploadCheck>

					<TextControl
						label={ __( 'URL de video de fondo (opcional)', 'vpinunf' ) }
						help={ __( 'Si se ingresa, el video tendrá prioridad sobre la imagen.', 'vpinunf' ) }
						value={ backgroundVideo }
						onChange={ ( val ) => setAttributes( { backgroundVideo: val } ) }
						type="url"
					/>

					<RangeControl
						label={ __( 'Opacidad del overlay oscuro', 'vpinunf' ) }
						value={ overlayOpacity }
						onChange={ ( val ) => setAttributes( { overlayOpacity: val } ) }
						min={ 0 }
						max={ 1 }
						step={ 0.05 }
					/>
				</PanelBody>

				{/* ── Logo del Programa ── */}
				<PanelBody title={ __( 'Logo del Programa', 'vpinunf' ) } initialOpen={ false }>
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
												{ __( 'Quitar logo', 'vpinunf' ) }
											</Button>
										</>
									) }
									<Button variant="primary" onClick={ open } style={ { width: '100%' } }>
										{ programLogo?.url
											? __( 'Cambiar logo', 'vpinunf' )
											: __( 'Seleccionar logo', 'vpinunf' ) }
									</Button>
								</div>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>

				{/* ── Contenido ── */}
				<PanelBody title={ __( 'Enlaces de Botones', 'vpinunf' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'URL botón primario', 'vpinunf' ) }
						value={ ctaPrimaryUrl }
						onChange={ ( val ) => setAttributes( { ctaPrimaryUrl: val } ) }
						type="url"
					/>
					<TextControl
						label={ __( 'URL botón secundario (opcional)', 'vpinunf' ) }
						value={ ctaSecondaryUrl }
						onChange={ ( val ) => setAttributes( { ctaSecondaryUrl: val } ) }
						type="url"
					/>
				</PanelBody>

				{/* ── Colores Avanzados ── */}
				<PanelColorSettings
					title={ __( 'Colores de Elementos', 'vpinunf' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: subtitleColor,
							onChange: ( val ) => setAttributes( { subtitleColor: val } ),
							label: __( 'Color del Subtítulo', 'vpinunf' ),
						},
						{
							value: ctaPrimaryBgColor,
							onChange: ( val ) => setAttributes( { ctaPrimaryBgColor: val } ),
							label: __( 'Fondo de Botón Primario', 'vpinunf' ),
						},
						{
							value: ctaPrimaryTextColor,
							onChange: ( val ) => setAttributes( { ctaPrimaryTextColor: val } ),
							label: __( 'Texto de Botón Primario', 'vpinunf' ),
						},
						{
							value: ctaSecondaryBorderColor,
							onChange: ( val ) => setAttributes( { ctaSecondaryBorderColor: val } ),
							label: __( 'Borde de Botón Secundario', 'vpinunf' ),
						},
						{
							value: ctaSecondaryTextColor,
							onChange: ( val ) => setAttributes( { ctaSecondaryTextColor: val } ),
							label: __( 'Texto de Botón Secundario', 'vpinunf' ),
						},
					] }
				/>
			</InspectorControls>

			{/* ── Vista del editor ── */}
			<div
				className="vpinunf-hero-lp-editor-preview"
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
					<RichText
						tagName="p"
						value={ subtitle }
						onChange={ ( val ) => setAttributes( { subtitle: val } ) }
						placeholder={ __( 'Subtítulo del Hero...', 'vpinunf' ) }
						style={ { color: subtitleColor, fontWeight: 700, fontSize: '1.4rem', textTransform: 'uppercase', letterSpacing: '0.15em', marginBottom: '0.8rem', outline: 'none' } }
					/>
					<RichText
						tagName="h1"
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
						placeholder={ __( '[ Título principal del programa ]', 'vpinunf' ) }
						style={ { color: '#ffffff', fontSize: '3.6rem', fontWeight: 800, marginBottom: '1.6rem', margin: '0 0 1.6rem', outline: 'none' } }
					/>
					<div style={ { display: 'flex', gap: '1.2rem', justifyContent: 'center', flexWrap: 'wrap' } }>
						<RichText
							tagName="span"
							value={ ctaPrimaryText }
							onChange={ ( val ) => setAttributes( { ctaPrimaryText: val } ) }
							placeholder={ __( 'Texto CTA Principal', 'vpinunf' ) }
							style={ { background: ctaPrimaryBgColor, color: ctaPrimaryTextColor, padding: '1.2rem 3rem', fontWeight: 700, fontSize: '1.5rem', borderRadius: '4px', outline: 'none', cursor: 'text' } }
						/>
						<RichText
							tagName="span"
							value={ ctaSecondaryText }
							onChange={ ( val ) => setAttributes( { ctaSecondaryText: val } ) }
							placeholder={ __( 'Texto CTA Secundario', 'vpinunf' ) }
							style={ { border: `2px solid ${ctaSecondaryBorderColor}`, color: ctaSecondaryTextColor, padding: '1.2rem 3rem', fontWeight: 700, fontSize: '1.5rem', borderRadius: '4px', outline: 'none', cursor: 'text' } }
						/>
					</div>
				</div>
			</div>
		</>
	);
}
