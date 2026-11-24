<?php
/**
 * The header for our theme
 *
 * @package vellajous
 */

$cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
	<style id="vellajous-critical-preloader-css">
		.site-logo img,
		.site-logo .custom-logo,
		.custom-logo,
		img.custom-logo {
			max-height: 38px !important;
			max-width: 210px !important;
			width: auto !important;
			height: auto !important;
			object-fit: contain !important;
			display: block !important;
		}
		.site-header__logo-wrap { max-width: 240px !important; min-width: 0 !important; overflow: hidden !important; }

		@media (max-width: 767px) {
			.site-logo img,
			.site-logo .custom-logo,
			.custom-logo,
			img.custom-logo {
				max-height: 25px !important;
				max-width: 142px !important;
			}
			.site-header__logo-wrap {
				max-width: 140px !important;
			}
		}

		body.vellajous-loading {
			/* Free from body overflow/height locking to ensure 100% fluid iOS momentum scrolling */
		}
		.vellajous-preloader {
			position: fixed !important;
			top: 0 !important;
			left: 0 !important;
			width: 100vw !important;
			height: 100vh !important;
			height: 100dvh !important;
			z-index: 9999999 !important;
			background: #ffffff !important;
			display: flex !important;
			align-items: center !important;
			justify-content: center !important;
			touch-action: none !important;
			opacity: 1;
			visibility: visible;
			transform: translateY(0);
			transition: transform 0.75s cubic-bezier(0.77, 0, 0.175, 1), opacity 0.5s ease 0.15s, visibility 0.75s ease;
			will-change: transform, opacity;
			user-select: none;
			-webkit-user-select: none;
		}
		.vellajous-preloader.is-loaded {
			transform: translateY(-100%) !important;
			opacity: 0 !important;
			visibility: hidden !important;
			pointer-events: none !important;
		}
		.vellajous-preloader__inner {
			position: relative;
			z-index: 5;
			text-align: center;
			padding: 24px;
			max-width: 440px;
			width: 90%;
			margin: 0 auto;
		}
		.preloader-brand__img {
			max-width: 240px;
			width: 78vw;
			height: auto;
			object-fit: contain;
			display: block;
			margin: 0 auto 30px auto;
			animation: preloaderLogoFadeIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
		}
		@keyframes preloaderLogoFadeIn {
			0% { opacity: 0; transform: translateY(10px) scale(0.96); }
			100% { opacity: 1; transform: translateY(0) scale(1); }
		}
		.preloader-brand__title {
			font-family: 'Cinzel', Georgia, serif;
			font-size: 19px;
			font-weight: 700;
			letter-spacing: 5px;
			color: #294742 !important;
			margin: 0 0 4px 0;
			text-transform: uppercase;
		}
		.preloader-brand__subtitle {
			display: block;
			font-family: 'Montserrat', sans-serif;
			font-size: 8.5px;
			font-weight: 600;
			letter-spacing: 3px;
			color: #c5a059;
			text-transform: uppercase;
			margin-bottom: 20px;
		}
		.preloader-counter-box {
			display: flex;
			align-items: baseline;
			justify-content: center;
			gap: 2px;
			margin-bottom: 12px;
		}
		.preloader-count {
			font-family: 'Cinzel', Georgia, serif;
			font-size: 24px;
			font-weight: 600;
			line-height: 1;
			color: #294742;
			letter-spacing: 0.5px;
			display: inline-block;
			font-variant-numeric: tabular-nums;
		}
		.preloader-unit {
			font-family: 'Montserrat', sans-serif;
			font-size: 13px;
			font-weight: 600;
			color: #c5a059;
		}
		.preloader-track {
			width: 160px;
			max-width: 70vw;
			height: 2px;
			background: #eaf0eb;
			border-radius: 4px;
			margin: 0 auto;
			position: relative;
			overflow: hidden;
		}
		.preloader-bar {
			height: 100%;
			width: 1%;
			background: linear-gradient(90deg, #294742 0%, #3d635c 60%, #c5a059 100%);
			border-radius: 4px;
			transition: width 0.08s ease-out;
		}
		@media (max-width: 480px) {
			.preloader-brand__img {
				max-width: 150px;
				margin-bottom: 16px;
			}
			.preloader-brand__title {
				font-size: 16px;
				letter-spacing: 3px;
			}
			.preloader-brand__subtitle {
				font-size: 7.5px;
				letter-spacing: 2px;
				margin-bottom: 14px;
			}
			.preloader-count {
				font-size: 20px;
				min-width: 38px;
			}
			.preloader-unit {
				font-size: 11px;
			}
			.preloader-track {
				width: 130px;
				height: 2px;
			}
		}
	</style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<!-- ======================================================================
	     LUXURY SITE PRELOADER (Light Theme & Brand Logo Sync)
	     ====================================================================== -->
	<?php
	$custom_logo_id = get_theme_mod('custom_logo');
	$preloader_logo_url = '';
	if ($custom_logo_id) {
		$logo_src = wp_get_attachment_image_src($custom_logo_id, 'full');
		if ($logo_src && !empty($logo_src[0])) {
			$preloader_logo_url = $logo_src[0];
		}
	}
	?>
	<div id="vellajousPreloader" class="vellajous-preloader" aria-live="polite">
		<div class="vellajous-preloader__inner">
			<!-- Official Brand Logo (Theme Green) -->
			<div class="vellajous-preloader__brand">
				<?php if (!empty($preloader_logo_url)) : ?>
					<img src="<?php echo esc_url($preloader_logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="preloader-brand__img" width="220" height="42" />
				<?php else : ?>
					<h1 class="preloader-brand__title">VELLAJOUS</h1>
					<span class="preloader-brand__subtitle">HAUTE PARFUMERIE</span>
				<?php endif; ?>
			</div>

			<!-- Counter & Progress Line -->
			<div class="vellajous-preloader__metric">
				<div class="preloader-counter-box">
					<span id="vellajousLoaderCount" class="preloader-count">1</span>
					<span class="preloader-unit">%</span>
				</div>
				<div class="preloader-track">
					<div id="vellajousLoaderBar" class="preloader-bar"></div>
				</div>
			</div>
		</div>
	</div>

	<script id="vellajous-preloader-script">
	(function() {
		var preloader = document.getElementById('vellajousPreloader');
		if (!preloader) return;

		var countEl = document.getElementById('vellajousLoaderCount');
		var barEl = document.getElementById('vellajousLoaderBar');

		document.body.classList.add('vellajous-loading');

		var currentVal = 1;
		var targetVal = 1;
		var videoReady = false;
		var completed = false;
		var startTimestamp = Date.now();

		function markReady() {
			if (videoReady) return;
			videoReady = true;
			targetVal = 100;
		}

		function bindVideoEvents(v) {
			if (!v) return;
			try {
				v.preload = 'auto';
				if (typeof v.load === 'function') v.load();
			} catch(e) {}

			if (v.readyState >= 3 || v.currentTime > 0) {
				markReady();
			} else {
				v.addEventListener('loadeddata', function() {
					targetVal = Math.max(targetVal, 75);
				});
				v.addEventListener('canplay', markReady);
				v.addEventListener('canplaythrough', markReady);
				v.addEventListener('playing', markReady);
			}
		}

		var initialVideo = document.getElementById('heroVideo');
		if (initialVideo) {
			bindVideoEvents(initialVideo);
		} else if (document.readyState === 'complete' || document.readyState === 'interactive') {
			var v = document.getElementById('heroVideo');
			if (v) {
				bindVideoEvents(v);
			} else {
				videoReady = true;
				targetVal = 100;
			}
		} else {
			document.addEventListener('DOMContentLoaded', function() {
				var v = document.getElementById('heroVideo');
				if (v) {
					bindVideoEvents(v);
				} else {
					videoReady = true;
					targetVal = 100;
				}
			});
		}

		var ticker = setInterval(function() {
			var elapsed = Date.now() - startTimestamp;

			if (!videoReady) {
				if (targetVal < 88) {
					targetVal += 2.2;
				}
				// Failsafe timeout at 1.8s to guarantee fast mobile experience
				if (elapsed >= 1800) {
					videoReady = true;
					targetVal = 100;
				}
			} else {
				targetVal = 100;
			}

			var delta = (targetVal - currentVal) * 0.25;
			if (delta < 1) delta = 1;
			currentVal += delta;

			if (currentVal >= 100) {
				currentVal = 100;
				endLoader();
			}

			var intVal = Math.floor(currentVal);
			if (countEl) countEl.textContent = intVal;
			if (barEl) barEl.style.width = currentVal + '%';
		}, 20);

		function endLoader() {
			if (completed) return;
			completed = true;
			clearInterval(ticker);

			if (countEl) countEl.textContent = '100';
			if (barEl) barEl.style.width = '100%';

			var isSliderMode = false;
			try {
				if (window.localStorage && localStorage.getItem('vellajous_hero_type') === 'slider') {
					isSliderMode = true;
				} else {
					var masterEl = document.getElementById('heroMasterBanner');
					if (masterEl && masterEl.getAttribute('data-active-type') === 'slider') {
						isSliderMode = true;
					}
				}
			} catch(e) {}

			var v = document.getElementById('heroVideo');
			if (v && !isSliderMode) {
				try {
					var p = v.play();
					if (p !== undefined) {
						p.catch(function() {
							v.muted = true;
							v.play();
						});
					}
				} catch(e) {}
			}

			setTimeout(function() {
				preloader.classList.add('is-loaded');
				document.body.classList.remove('vellajous-loading');
				document.body.classList.add('vellajous-loaded');

				setTimeout(function() {
					preloader.style.display = 'none';
				}, 800);
			}, 200);
		}
	})();
	</script>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'vellajous'); ?></a>

	<!-- Top Announcement Bar -->
	<div class="announcement-bar">
		<div class="container">
			<span class="announcement-desktop"><?php esc_html_e('Complimentary Express Shipping Across India', 'vellajous'); ?> &nbsp;•&nbsp; <strong><?php esc_html_e('Artisanal Luxury Perfumes', 'vellajous'); ?></strong></span>
			<span class="announcement-mobile"><?php esc_html_e('Express Shipping Across India', 'vellajous'); ?> &nbsp;•&nbsp; <strong><?php esc_html_e('Haute Parfumerie', 'vellajous'); ?></strong></span>
		</div>
	</div>

	<!-- Main Navigation Header -->
	<header id="masthead" class="site-header">
		<div class="container site-header__inner">
			<!-- Mobile Left: Hamburger Menu (3 lines) -->
			<div class="site-header__mobile-left">
				<button type="button" class="mobile-nav-toggle mobile-hamburger-btn" id="mobileNavToggle" aria-label="<?php esc_attr_e('Open navigation menu', 'vellajous'); ?>" title="<?php esc_attr_e('Menu', 'vellajous'); ?>">
					<span class="hamburger-line"></span>
					<span class="hamburger-line"></span>
					<span class="hamburger-line"></span>
				</button>
			</div>

			<!-- Brand Logo (Center on mobile, Left on desktop) -->
			<div class="site-header__left site-header__logo-wrap">
				<div class="site-logo">
					<?php if (has_custom_logo()) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
							<span class="site-logo__title">VELLAJOUS</span>
							<span class="site-logo__subtitle">HAUTE PARFUMERIE</span>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Center: Navigation Menu (Desktop only) -->
			<div class="site-header__center">
				<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Primary Menu', 'vellajous'); ?>">
					<?php
					if (has_nav_menu('menu-1')) {
						wp_nav_menu(array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'site-nav',
							'container'      => false,
						));
					} else {
						$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
						?>
						<ul class="site-nav">
							<li class="menu-item-has-children">
								<a href="#"><?php esc_html_e('Shop', 'vellajous'); ?></a>
								<ul class="sub-menu">
									<li class="menu-item-has-children">
										<a href="#"><?php esc_html_e('WHO ARE YOU?', 'vellajous'); ?></a>
										<ul class="sub-menu">
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('healthcare-public-service', 'Healthcare & Public Service')); ?>"><?php esc_html_e('Healthcare & Public Service', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('business-professional', 'Business & Professional')); ?>"><?php esc_html_e('Business & Professional', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('design-build', 'Design & Build')); ?>"><?php esc_html_e('Design & Build', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('travel-adventure', 'Travel & Adventure')); ?>"><?php esc_html_e('Travel & Adventure', 'vellajous'); ?></a></li>
										</ul>
									</li>
									<li class="menu-item-has-children">
										<a href="#"><?php esc_html_e('HOW DO YOU FEEL?', 'vellajous'); ?></a>
										<ul class="sub-menu">
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('calm-wind-down', 'Calm & Wind Down')); ?>"><?php esc_html_e('Calm & Wind Down', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('focus-reset', 'Focus & Reset')); ?>"><?php esc_html_e('Focus & Reset', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('energy-confidence', 'Energy & Confidence')); ?>"><?php esc_html_e('Energy & Confidence', 'vellajous'); ?></a></li>
											<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('bold-magnetic', 'Bold & Magnetic')); ?>"><?php esc_html_e('Bold & Magnetic', 'vellajous'); ?></a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li><a href="<?php echo esc_url(home_url('/about-us/')); ?>"><?php esc_html_e('About Us', 'vellajous'); ?></a></li>
							<li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>"><?php esc_html_e('Contact Us', 'vellajous'); ?></a></li>
						</ul>
						<?php
					}
					?>
				</nav>
			</div>

			<!-- Right Utilities: Desktop & Mobile Shopping Bag -->
			<div class="header-actions">
				<!-- Search (Desktop only) -->
				<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="header-action-btn desktop-only" title="<?php esc_attr_e('Search Perfumes', 'vellajous'); ?>" aria-label="<?php esc_attr_e('Search', 'vellajous'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
						<circle cx="11" cy="11" r="7"></circle>
						<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
					</svg>
				</a>

				<!-- Account (Desktop only) -->
				<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="header-action-btn header-account-btn desktop-only" title="<?php esc_attr_e('My Account', 'vellajous'); ?>" aria-label="<?php esc_attr_e('Account', 'vellajous'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
						<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
						<circle cx="12" cy="7" r="4"></circle>
					</svg>
				</a>

				<!-- Wishlist (Desktop only) -->
				<a href="<?php echo esc_url(home_url('/wishlist/')); ?>" class="header-action-btn header-wishlist-btn desktop-only" title="<?php esc_attr_e('My Wishlist', 'vellajous'); ?>" aria-label="<?php esc_attr_e('Wishlist', 'vellajous'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
						<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
					</svg>
					<span class="vellajous-wishlist-count" style="display:none;">0</span>
				</a>

				<!-- Cart Drawer Button (Desktop & Mobile Right) -->
				<button type="button" class="header-action-btn header-cart-btn mobile-header-cart-right" title="<?php esc_attr_e('Shopping Bag', 'vellajous'); ?>" aria-label="<?php esc_attr_e('Cart', 'vellajous'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
						<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<path d="M16 10a4 4 0 0 1-8 0"></path>
					</svg>
					<span class="vellajous-cart-count"><?php echo esc_html($cart_count); ?></span>
				</button>
			</div>
		</div>
	</header>

	<!-- Mobile Nav Drawer & Overlay -->
	<div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
	<div class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
		<div class="mobile-nav-header">
			<div class="mobile-nav-brand">
				<span class="site-logo__title">VELLAJOUS</span>
				<span class="site-logo__subtitle">HAUTE PARFUMERIE</span>
			</div>
			<button type="button" class="mobile-nav-close" id="mobileNavClose" aria-label="<?php esc_attr_e('Close navigation menu', 'vellajous'); ?>">&times;</button>
		</div>
		<div class="mobile-nav-body">
			<ul class="mobile-nav-links">
				<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a></li>
				<li><a href="<?php echo esc_url(home_url('/about-us/')); ?>"><?php esc_html_e('About Us', 'vellajous'); ?></a></li>
				<li class="mobile-item-has-children">
					<div class="mobile-nav-link-row">
						<a href="#" class="mobile-main-link"><?php esc_html_e('Shop', 'vellajous'); ?></a>
						<button type="button" class="mobile-submenu-arrow" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle Shop Submenu', 'vellajous'); ?>">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
						</button>
					</div>
					<ul class="mobile-sub-menu">
						<!-- Item 1: WHO ARE YOU? -->
						<li class="mobile-sub-item-has-children">
							<div class="mobile-nav-link-row mobile-sub-row">
								<span class="mobile-sub-heading"><?php esc_html_e('WHO ARE YOU?', 'vellajous'); ?></span>
								<button type="button" class="mobile-submenu-arrow" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle Who Are You Submenu', 'vellajous'); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
								</button>
							</div>
							<ul class="mobile-nested-sub-menu">
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('healthcare-public-service', 'Healthcare & Public Service')); ?>"><?php esc_html_e('Healthcare & Public Service', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('business-professional', 'Business & Professional')); ?>"><?php esc_html_e('Business & Professional', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('design-build', 'Design & Build')); ?>"><?php esc_html_e('Design & Build', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('travel-adventure', 'Travel & Adventure')); ?>"><?php esc_html_e('Travel & Adventure', 'vellajous'); ?></a></li>
							</ul>
						</li>

						<!-- Item 2: HOW DO YOU FEEL? -->
						<li class="mobile-sub-item-has-children">
							<div class="mobile-nav-link-row mobile-sub-row">
								<span class="mobile-sub-heading"><?php esc_html_e('HOW DO YOU FEEL?', 'vellajous'); ?></span>
								<button type="button" class="mobile-submenu-arrow" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle How Do You Feel Submenu', 'vellajous'); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
								</button>
							</div>
							<ul class="mobile-nested-sub-menu">
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('calm-wind-down', 'Calm & Wind Down')); ?>"><?php esc_html_e('Calm & Wind Down', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('focus-reset', 'Focus & Reset')); ?>"><?php esc_html_e('Focus & Reset', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('energy-confidence', 'Energy & Confidence')); ?>"><?php esc_html_e('Energy & Confidence', 'vellajous'); ?></a></li>
								<li><a href="<?php echo esc_url(class_vellajous_woocommerce::get_category_url('bold-magnetic', 'Bold & Magnetic')); ?>"><?php esc_html_e('Bold & Magnetic', 'vellajous'); ?></a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>"><?php esc_html_e('Contact Us', 'vellajous'); ?></a></li>
				<li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('My Account', 'vellajous'); ?></a></li>
			</ul>
		</div>
		<div class="mobile-nav-footer">
			<div class="mobile-nav-badge">
				<span><?php esc_html_e('Dubai Haute Parfumerie', 'vellajous'); ?></span>
			</div>
			<div class="mobile-nav-contact">
				<a href="mailto:care@vellajous.com">care@vellajous.com</a>
			</div>
		</div>
	</div>