<?php do_action('viceunf_site_preloader'); ?>
<?php do_action('viceunf_wp_hdr_image'); ?>
<?php
$viceunf_sticky_header = get_theme_mod('viceunf_sticky_header', '1');
?>
<header id="dt_header" class="dt_header header--eight">
	<div class="dt_header-inner">
		<div class="dt_header-topbar dt-d-lg-block dt-d-none">
			<?php do_action('viceunf_site_header'); ?>
		</div>
		<div class="dt_header-navwrapper">
			<div class="dt_header-navwrapperinner">
				<!--=== / Inicio: DT_Navbar / === -->
				<div class="dt_navbar dt-d-none dt-d-lg-block">
					<div class="dt_navbar-wrapper <?php if ($viceunf_sticky_header == '1'): esc_attr_e('is--sticky', 'viceunf');
																			endif; ?>">
						<div class="dt-container">
							<div class="dt-row">
								<div class="dt-col-2">
									<div class="site--logo">
										<?php do_action('viceunf_site_logo'); ?>
									</div>
								</div>
								<div class="dt-col-10 dt-my-auto">
									<div class="dt_navbar-menu">
										<nav class="dt_navbar-nav">
											<?php do_action('viceunf_site_header_navigation'); ?>
										</nav>
										<div class="dt_navbar-right">
											<ul class="dt_navbar-list-right">
												<?php do_action('viceunf_woo_cart'); ?>
												<?php do_action('viceunf_site_main_search'); ?>
												<?php do_action('viceunf_hdr_account'); ?>
												<?php do_action('viceunf_header_button'); ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--=== / Fin: DT_Navbar / === -->
				<!--=== / Inicio: DT_Menú Móvil / === -->
				<div class="dt_mobilenav <?php if ($viceunf_sticky_header == '1'): esc_attr_e('is--sticky', 'viceunf'); endif; ?> dt-d-lg-none">
					<!-- Topbar móvil eliminada por considerarse redudante (UI/UX) -->
					<div class="dt-container">
						<div class="dt-row">
							<div class="dt-col-12">
								<div class="dt_mobilenav-menu dt-d-flex dt-justify-content-between dt-align-items-center">
									<div class="dt_mobilenav-logo">
										<div class="site--logo">
											<?php do_action('viceunf_site_mobile_logo'); ?>
										</div>
									</div>
									<div class="dt_mobilenav-toggles dt-d-flex dt-align-items-center">
										<div class="dt_mobilenav-right">
											<ul class="dt_navbar-list-right">
												<?php do_action('viceunf_site_main_search'); ?>
												<?php do_action('viceunf_header_button'); ?>
											</ul>
										</div>
										<div class="dt_mobilenav-mainmenu">
											<button type="button" aria-expanded="false" class="hamburger dt_mobilenav-mainmenu-toggle" aria-label="Abrir menú" aria-controls="mobile-menu-content">
												<span></span>
												<span></span>
												<span></span>
											</button>
											<div class="off--layer"></div>
											<nav id="mobile-menu-content" class="dt_mobilenav-mainmenu-content" aria-hidden="true">
												<div class="dt_mobilenav-mainmenu-inner">
													<div class="dt_mobilenav-header">
														<div class="site--logo">
															<?php do_action('viceunf_site_mobile_logo'); ?>
														</div>
														<button type="button" class="dt_header-closemenu site--close" aria-label="Cerrar menú" aria-controls="mobile-menu-content"></button>
													</div>
													<div class="dt_mobilenav-scroll">
														<?php do_action('viceunf_site_header_navigation'); ?>
													</div>
												</div>
											</nav>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--=== / Fin: DT_Menú Móvil / === -->
			</div>
		</div>
	</div>
</header>