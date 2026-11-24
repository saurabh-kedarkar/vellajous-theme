<?php
/**
 * Template Name: Front Page Template
 * The template for displaying the home page
 *
 * @package vellajous
 */

get_header();

/**
 * ======================================================================
 * RETRIEVE ACF SECTION-BY-SECTION GROUPS SAFELY
 * ======================================================================
 */
$hero_group       = function_exists('get_field') ? get_field('hero_section') : null;
$best_sellers     = function_exists('get_field') ? get_field('best_sellers_section') : null;
$pillars_group    = function_exists('get_field') ? get_field('assurance_pillars_section') : null;
$spotlight_group  = function_exists('get_field') ? get_field('spotlight_section') : null;
$attitude_group   = function_exists('get_field') ? get_field('attitude_section') : null;
$reviews_group    = function_exists('get_field') ? get_field('reviews_section') : null;
$newsletter_group = function_exists('get_field') ? get_field('newsletter_section') : null;

/**
 * ======================================================================
 * HERO BANNER MODE & SLIDES CONFIGURATION
 * ======================================================================
 */
$default_banner_mode = !empty($hero_group['banner_type']) ? $hero_group['banner_type'] : get_theme_mod('vellajous_hero_banner_type', 'slider');
$banner_type = isset($_GET['banner']) ? sanitize_text_field($_GET['banner']) : $default_banner_mode;
if (!in_array($banner_type, array('slider', 'video'), true)) {
	$banner_type = 'slider';
}

$video_url = !empty($hero_group['video_url']) ? $hero_group['video_url'] : get_theme_mod('vellajous_hero_video_url', 'https://www.naseemperfume.in/cdn/shop/videos/c/vp/ea98af742d1e4af7af7e1859281b0c7b/ea98af742d1e4af7af7e1859281b0c7b.HD-1080p-7.2Mbps-57379405.mp4?v=0');
$poster_url = !empty($hero_group['poster_image']) ? class_vellajous_acf::get_image_url($hero_group['poster_image']) : 'https://www.naseemperfume.in/cdn/shop/files/preview_images/ea98af742d1e4af7af7e1859281b0c7b.thumbnail.0000000000_small.jpg?v=1757742019';
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
$upload_dir = wp_upload_dir();

// Dynamic Hero Slides via ACF Repeater with graceful luxury fallback
$slider_slides = array();
if (!empty($hero_group['slides']) && is_array($hero_group['slides'])) {
	foreach ($hero_group['slides'] as $s) {
		$slider_slides[] = array(
			'image'         => class_vellajous_acf::get_image_url($s['image'] ?? '', $upload_dir['baseurl'] . '/2026/10/banner-light-cool-flow.jpg'),
			'image_mobile'  => class_vellajous_acf::get_image_url($s['image_mobile'] ?? '', $upload_dir['baseurl'] . '/2026/10/banner-mobile-cool-flow.jpg'),
			'tagline'       => !empty($s['tagline']) ? $s['tagline'] : __('COOL FLOW · EXTRAIT DE PARFUM', 'vellajous'),
			'heading'       => !empty($s['heading']) ? $s['heading'] : __('MADE FOR YOU', 'vellajous'),
			'subtext'       => !empty($s['subtext']) ? $s['subtext'] : '',
			'btn_primary'   => !empty($s['btn_primary']) ? $s['btn_primary'] : __('Shop Now', 'vellajous'),
			'btn_link'      => !empty($s['btn_link']) ? $s['btn_link'] : '#best-sellers',
			'btn_secondary' => !empty($s['btn_secondary']) ? $s['btn_secondary'] : __('Explore Collection', 'vellajous'),
			'btn_sec_link'  => !empty($s['btn_sec_link']) ? $s['btn_sec_link'] : $shop_url,
		);
	}
}

// Fallback to default 3 luxury slides if repeater is empty
if (empty($slider_slides)) {
	$slider_slides = array(
		array(
			'image'         => $upload_dir['baseurl'] . '/2026/10/banner-light-cool-flow.jpg',
			'image_mobile'  => $upload_dir['baseurl'] . '/2026/10/banner-mobile-cool-flow.jpg',
			'tagline'       => __('COOL FLOW · EXTRAIT DE PARFUM', 'vellajous'),
			'heading'       => __('MADE FOR YOU', 'vellajous'),
			'subtext'       => __('A bold contemporary fragrance statement. Pure crisp sillage and magnetic presence crafted for the modern connoisseur.', 'vellajous'),
			'btn_primary'   => __('Shop Now', 'vellajous'),
			'btn_link'      => '#best-sellers',
			'btn_secondary' => __('Explore Collection', 'vellajous'),
			'btn_sec_link'  => $shop_url,
		),
		array(
			'image'         => $upload_dir['baseurl'] . '/2026/10/banner-light-made-for-you-v3.jpg',
			'image_mobile'  => $upload_dir['baseurl'] . '/2026/10/banner-mobile-mystique.jpg',
			'tagline'       => __('HAUTE PARFUMERIE · DUBAI ATELIER', 'vellajous'),
			'heading'       => __('INTO THE MYSTIQUE', 'vellajous'),
			'subtext'       => __('Pure water-based aqua parfums and royal concentrated attars formulated to endure all day on your skin.', 'vellajous'),
			'btn_primary'   => __('Shop Now', 'vellajous'),
			'btn_link'      => '#best-sellers',
			'btn_secondary' => __('Discover All Scents', 'vellajous'),
			'btn_sec_link'  => $shop_url,
		),
		array(
			'image'         => $upload_dir['baseurl'] . '/2026/10/banner-light-signature.jpg',
			'image_mobile'  => $upload_dir['baseurl'] . '/2026/10/banner-mobile-signature.jpg',
			'tagline'       => __('NEW CAMPAIGN ', 'vellajous'),
			'heading'       => __('SIGNATURE ATELIER', 'vellajous'),
			'subtext'       => __('Luxury Extrait de Parfum infused with rare botanical extracts and captivating modern sillage.', 'vellajous'),
			'btn_primary'   => __('Shop Now', 'vellajous'),
			'btn_link'      => $shop_url,
			'btn_secondary' => __('View All Scents', 'vellajous'),
			'btn_sec_link'  => $shop_url,
		),
	);
}
?>

<main id="primary" class="site-main">

	<!-- ======================================================================
	     HERO MASTER BANNER (Light Theme Editorial Campaign: Slider & Video)
	     ====================================================================== -->
	<section class="hero-master-banner hero-master-banner--light" id="heroMasterBanner" data-active-type="<?php echo esc_attr($banner_type); ?>">

		<?php if ($banner_type === 'slider') : ?>
		<!-- Media Layer: Image Slider Banner -->
		<div class="hero-slider-banner hero-media-layer is-active" id="heroSliderLayer">
			<div class="hero-slider-track" id="heroSliderTrack">
				<?php foreach ($slider_slides as $index => $slide) : ?>
					<div class="hero-slide <?php echo ($index === 0) ? 'is-active' : ''; ?>" data-slide-index="<?php echo esc_attr($index); ?>">
						<div class="hero-slide__image-wrap">
							<picture>
								<?php if (!empty($slide['image_mobile'])) : ?>
									<source media="(max-width: 767px)" srcset="<?php echo esc_url($slide['image_mobile']); ?>">
								<?php endif; ?>
								<img src="<?php echo esc_url($slide['image']); ?>" 
								     alt="<?php echo esc_attr($slide['heading']); ?>" 
								     class="hero-slide__image" 
								     loading="<?php echo ($index === 0) ? 'eager' : 'lazy'; ?>" 
								     fetchpriority="<?php echo ($index === 0) ? 'high' : 'auto'; ?>" />
							</picture>
							<div class="hero-slide__overlay"></div>
						</div>
						<div class="hero-slide__content">
							<span class="hero-slide__tagline"><?php echo esc_html($slide['tagline']); ?></span>
							<!-- <h1 class="hero-slide__heading"><?php echo esc_html($slide['heading']); ?></h1> -->
							<p class="hero-slide__subtext"><?php echo esc_html($slide['subtext']); ?></p>
							<div class="hero-slide__cta-group">
								<a href="<?php echo esc_url($slide['btn_link']); ?>" class="vellajous-btn vellajous-btn--primary">
									<?php echo esc_html($slide['btn_primary']); ?>
								</a>
								<!-- <a href="<?php echo esc_url($slide['btn_sec_link']); ?>" class="vellajous-btn vellajous-btn--white">
									<?php echo esc_html($slide['btn_secondary']); ?>
								</a> -->
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Slider Controls: Previous / Next Arrows -->
			<button type="button" class="hero-slider-arrow hero-slider-prev" id="heroSlidePrev" aria-label="<?php esc_attr_e('Previous slide', 'vellajous'); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<button type="button" class="hero-slider-arrow hero-slider-next" id="heroSlideNext" aria-label="<?php esc_attr_e('Next slide', 'vellajous'); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>

			<!-- Slider Pagination Dots -->
			<div class="hero-slider-dots" id="heroSliderDots">
				<?php foreach ($slider_slides as $index => $slide) : ?>
					<button type="button" class="hero-slider-dot <?php echo ($index === 0) ? 'is-active' : ''; ?>" data-slide-to="<?php echo esc_attr($index); ?>" aria-label="<?php printf(esc_attr__('Go to slide %d', 'vellajous'), $index + 1); ?>"></button>
				<?php endforeach; ?>
			</div>
		</div>

		<?php else : ?>
		<!-- Media Layer: Video Banner -->
		<div class="hero-video-banner hero-media-layer is-active" id="heroVideoLayer">
			<div class="hero-video-banner__media">
				<video id="heroVideo" 
				       class="hero-video-banner__video" 
				       autoplay 
				       muted 
				       loop 
				       playsinline 
				       preload="auto" 
				       poster="<?php echo esc_url($poster_url); ?>">
					<source src="<?php echo esc_url($video_url); ?>" type="video/mp4" />
					<img src="<?php echo esc_url($poster_url); ?>" alt="<?php esc_attr_e('Vellajous Luxury Parfums', 'vellajous'); ?>" />
				</video>
				<div class="hero-video-banner__overlay"></div>
			</div>

			<div class="hero-video-banner__content">
				<span class="hero-video-banner__tagline"><?php esc_html_e('Haute Parfumerie · Dubai Atelier', 'vellajous'); ?></span>
				<h1 class="hero-video-banner__heading"><?php esc_html_e('INTO THE MYSTIQUE', 'vellajous'); ?></h1>
				<p class="hero-video-banner__subtext">
					<?php esc_html_e('Experience the artistry of pure water-based aqua parfums and royal concentrated oils designed to endure all day on your skin.', 'vellajous'); ?>
				</p>
				<div class="hero-video-banner__cta-group">
					<a href="#best-sellers" class="vellajous-btn vellajous-btn--primary">
						<?php esc_html_e('Shop Best Sellers', 'vellajous'); ?>
					</a>
					<a href="<?php echo esc_url($shop_url); ?>" class="vellajous-btn vellajous-btn--white">
						<?php esc_html_e('Explore All Scents', 'vellajous'); ?>
					</a>
				</div>
			</div>

			<!-- Sound Toggle Button -->
			<button type="button" class="hero-sound-toggle" id="heroSoundToggle" title="<?php esc_attr_e('Toggle Sound', 'vellajous'); ?>" aria-label="<?php esc_attr_e('Toggle video sound', 'vellajous'); ?>">
				<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
					<line x1="23" y1="9" x2="17" y2="15"></line>
					<line x1="17" y1="9" x2="23" y2="15"></line>
				</svg>
			</button>
		</div>
		<?php endif; ?>

	</section>

	<!-- ======================================================================
	     SECTION 1: BEST SELLERS (Alternate Section: Off-white #f7f9f7)
	     Matches user screenshot: ASMIRA, Eclipse, SUBLIME OUD with hover image
	     ====================================================================== -->
	<?php
	$bs_kicker = !empty($best_sellers['kicker']) ? $best_sellers['kicker'] : __('Iconic Fragrances', 'vellajous');
	$bs_title  = !empty($best_sellers['title']) ? $best_sellers['title'] : __('BEST SELLERS', 'vellajous');
	$bs_desc   = !empty($best_sellers['description']) ? $best_sellers['description'] : __('Our most acclaimed artisanal masterpieces, celebrated for their captivating presence and exquisite complexity.', 'vellajous');
	$bs_btn_t  = !empty($best_sellers['btn_text']) ? $best_sellers['btn_text'] : __('View All', 'vellajous');
	$bs_btn_l  = !empty($best_sellers['btn_link']) ? $best_sellers['btn_link'] : $shop_url;
	?>
	<section class="section section--alt" id="best-sellers">
		<div class="container">
			<div class="section-header">
				<span class="section-header__kicker"><?php echo esc_html($bs_kicker); ?></span>
				<h2 class="section-header__title"><?php echo esc_html($bs_title); ?></h2>
				<p class="section-header__desc">
					<?php echo esc_html($bs_desc); ?>
				</p>
			</div>

			<div class="vellajous-products-grid">
				<?php
				$best_seller_query = new WP_Query([
					'post_type'      => 'product',
					'posts_per_page' => 4,
					'orderby'        => 'date',
					'order'          => 'ASC',
					'tax_query'      => [
						[
							'taxonomy' => 'product_cat',
							'field'    => 'slug',
							'terms'    => 'best-sellers',
						],
					],
				]);

				if (!$best_seller_query->have_posts()) {
					// Fallback query if category not set
					$best_seller_query = new WP_Query([
						'post_type'      => 'product',
						'posts_per_page' => 4,
						'orderby'        => 'date',
						'order'          => 'ASC',
					]);
				}

				if ($best_seller_query->have_posts()) :
					while ($best_seller_query->have_posts()) :
						$best_seller_query->the_post();
						echo class_vellajous_woocommerce::render_product_card(get_the_ID());
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>

			<div style="text-align: center; margin-top: 48px;">
				<a href="<?php echo esc_url($bs_btn_l); ?>" class="vellajous-btn vellajous-btn--primary">
					<?php echo esc_html($bs_btn_t); ?>
				</a>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     LUXURY PILLARS / ASSURANCE CARDS (Global Common Section)
	     ====================================================================== -->
	<?php get_template_part('template-parts/section', 'assurance-pillars'); ?>

	<!-- ======================================================================
	     SECTION 3: BRAND SPOTLIGHT (Signature Masterpiece - UNIFORM)
	     ====================================================================== -->
	<?php
	$spot_badge      = !empty($spotlight_group['prestige_badge']) ? $spotlight_group['prestige_badge'] : __('SIGNATURE MASTERPIECE', 'vellajous');
	$spot_image      = !empty($spotlight_group['product_image']) ? class_vellajous_acf::get_image_url($spotlight_group['product_image']) : 'https://www.naseemperfume.in/cdn/shop/files/eclipsenewconcept1.jpg?v=1776246934&width=1100';
	$spot_title      = !empty($spotlight_group['title']) ? $spotlight_group['title'] : __('UNIFORM', 'vellajous');
	$spot_conc       = !empty($spotlight_group['concentration_tag']) ? $spotlight_group['concentration_tag'] : __('EXTRAIT DE PARFUM · 50ML · 30% OIL', 'vellajous');
	$spot_prod_id    = !empty($spotlight_group['product_id']) ? (int)$spotlight_group['product_id'] : 84;
	$spot_top_note   = !empty($spotlight_group['top_note']) ? $spotlight_group['top_note'] : __('Bergamot', 'vellajous');
	$spot_base_note  = !empty($spotlight_group['base_note']) ? $spotlight_group['base_note'] : __('Ambergris', 'vellajous');
	$spot_curr_price = !empty($spotlight_group['current_price']) ? $spotlight_group['current_price'] : '₹2,499';
	$spot_reg_price  = !empty($spotlight_group['regular_price']) ? $spotlight_group['regular_price'] : '₹2,999';
	$spot_save_badge = !empty($spotlight_group['save_badge']) ? $spotlight_group['save_badge'] : __('SAVE ₹500', 'vellajous');
	$spot_f1_title   = !empty($spotlight_group['feature_1_title']) ? $spotlight_group['feature_1_title'] : __('Pure & Gentle:', 'vellajous');
	$spot_f1_desc    = !empty($spotlight_group['feature_1_desc']) ? $spotlight_group['feature_1_desc'] : __('Hydrating, skin-loving, and non-sensitizing on delicate skin.', 'vellajous');
	$spot_f2_title   = !empty($spotlight_group['feature_2_title']) ? $spotlight_group['feature_2_title'] : __('8Hr+ Longevity:', 'vellajous');
	$spot_f2_desc    = !empty($spotlight_group['feature_2_desc']) ? $spotlight_group['feature_2_desc'] : __('High-density 30% oil concentration for all-day projection.', 'vellajous');
	$spot_btn_text   = !empty($spotlight_group['btn_text']) ? $spotlight_group['btn_text'] : __('Explore Details', 'vellajous');
	$spot_btn_link   = !empty($spotlight_group['btn_link']) ? $spotlight_group['btn_link'] : home_url('/product/uniform/');
	?>
	<section class="section section--alt vellajous-spotlight-section" id="about">
		<div class="container">
			<div class="spotlight-highlight-card">
				<!-- Top Floating Prestige Pill -->
				<div class="spotlight-badge-wrap">
					<span class="spotlight-prestige-badge">
						<span class="badge-star">★</span> <?php echo esc_html($spot_badge); ?>
					</span>
				</div>

				<div class="spotlight-grid">
					<!-- Media Side -->
					<div class="spotlight-media">
						<div class="spotlight-media-inner">
							<img src="<?php echo esc_url($spot_image); ?>" 
							     alt="<?php echo esc_attr($spot_title); ?>" 
							     class="spotlight-media-img"
							     loading="lazy" />
							
							<!-- Scent Concentration Pill -->
							<div class="spotlight-concentration-pill">
								<span><?php echo esc_html($spot_conc); ?></span>
							</div>

							<!-- Floating Wishlist Button -->
							<button type="button" 
							        class="vellajous-wishlist-btn spotlight-floating-wishlist" 
							        data-product-id="<?php echo esc_attr($spot_prod_id); ?>" 
							        data-product-title="<?php echo esc_attr($spot_title); ?>"
							        title="<?php echo esc_attr(sprintf(__('Add %s to Wishlist', 'vellajous'), $spot_title)); ?>" 
							        aria-label="<?php echo esc_attr(sprintf(__('Wishlist %s', 'vellajous'), $spot_title)); ?>">
								<svg class="wishlist-icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none">
									<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
								</svg>
							</button>
						</div>
					</div>

					<!-- Content Side -->
					<div class="spotlight-content">
						<h2 class="spotlight-title"><?php echo esc_html($spot_title); ?></h2>

						<!-- Fragrance Notes Capsule -->
						<div class="spotlight-notes-bar">
							<span class="note-pill"><span class="note-type"></span> <?php echo esc_html($spot_top_note); ?></span>
							<span class="note-pill"><span class="note-type"></span> <?php echo esc_html($spot_base_note); ?></span>
						</div>

						<!-- Pricing Row -->
						<div class="spotlight-price-row">
							<span class="spotlight-current-price"><?php echo esc_html($spot_curr_price); ?></span>
							<span class="spotlight-regular-price"><?php echo esc_html($spot_reg_price); ?></span>
							<span class="spotlight-save-badge"><?php echo esc_html($spot_save_badge); ?></span>
						</div>

						<ul class="spotlight-features">
							<li>
								<div class="feature-check-icon">
									<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
								</div>
								<span><strong><?php echo esc_html($spot_f1_title); ?></strong> <?php echo esc_html($spot_f1_desc); ?></span>
							</li>	
							<li>
								<div class="feature-check-icon">
									<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
								</div>
								<span><strong><?php echo esc_html($spot_f2_title); ?></strong> <?php echo esc_html($spot_f2_desc); ?></span>
							</li>
						</ul>

						<!-- Action Group -->
						<div class="spotlight-actions-group">
							<a href="<?php echo esc_url($spot_btn_link); ?>" class="vellajous-btn vellajous-btn--primary">
								<?php echo esc_html($spot_btn_text); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ======================================================================
	     SECTION 5: MATCH YOUR ATTITUDE & REAL WEAR TESTS (Screenshot 2)
	     ====================================================================== -->
	<?php
	$att_kicker = !empty($attitude_group['kicker']) ? $attitude_group['kicker'] : __('Curated By Personality', 'vellajous');
	$att_title  = !empty($attitude_group['title'])  ? $attitude_group['title']  : __('MATCH YOUR ATTITUDE', 'vellajous');
	$attitude_base = $upload_dir['baseurl'] . '/attitude/';

	$attitude_cards = [];
	if (!empty($attitude_group['cards']) && is_array($attitude_group['cards'])) {
		foreach ($attitude_group['cards'] as $c) {
			$attitude_cards[] = [
				'img'   => class_vellajous_acf::get_image_url($c['image'] ?? '', $attitude_base . 'hustler.jpg'),
				'alt'   => class_vellajous_acf::get_image_alt($c['image'] ?? '', $c['label'] ?? 'Attitude'),
				'label' => !empty($c['label']) ? $c['label'] : '',
				'tag'   => !empty($c['tag']) ? $c['tag'] : '',
				'link'  => !empty($c['link']) ? $c['link'] : $shop_url,
			];
		}
	}

	if (empty($attitude_cards)) {
		$attitude_cards = [
			[
				'img'   => $attitude_base . 'hustler.jpg',
				'alt'   => 'Hustler',
				'label' => __('HUSTLER', 'vellajous'),
				'tag'   => __('Bold & Focused', 'vellajous'),
				'link'  => $shop_url,
			],
			[
				'img'   => $attitude_base . 'gentleman.jpg',
				'alt'   => 'Gentleman',
				'label' => __('GENTLEMAN', 'vellajous'),
				'tag'   => __('Classic & Refined', 'vellajous'),
				'link'  => $shop_url,
			],
			[
				'img'   => $attitude_base . 'sporty.jpg',
				'alt'   => 'Sporty',
				'label' => __('SPORTY', 'vellajous'),
				'tag'   => __('Fresh & Energetic', 'vellajous'),
				'link'  => $shop_url,
			],
			[
				'img'   => $attitude_base . 'adventurer.jpg',
				'alt'   => 'Adventurer',
				'label' => __('ADVENTURER', 'vellajous'),
				'tag'   => __('Raw & Mysterious', 'vellajous'),
				'link'  => $shop_url,
			],
			[
				'img'   => $attitude_base . 'bold.jpg',
				'alt'   => 'Bold',
				'label' => __('BOLD', 'vellajous'),
				'tag'   => __('Intense & Charismatic', 'vellajous'),
				'link'  => $shop_url,
			],
			[
				'img'   => $attitude_base . 'socializer.jpg',
				'alt'   => 'Socializer',
				'label' => __('SOCIALIZER', 'vellajous'),
				'tag'   => __('Warm & Magnetic', 'vellajous'),
				'link'  => $shop_url,
			],
		];
	}
	?>
	<section class="attitude-weartest-section" id="attitude-weartest">
		<div class="container">
			<!-- Header -->
			<div class="attitude-header">
				<span class="attitude-kicker"><?php echo esc_html($att_kicker); ?></span>
				<h2 class="attitude-title"><?php echo esc_html($att_title); ?></h2>
			</div>

			<!-- Attitude Cards Grid (Repeater Powered) -->
			<div class="attitude-grid">
				<?php foreach ($attitude_cards as $card) : ?>
					<a href="<?php echo esc_url($card['link']); ?>" class="attitude-card">
						<div class="attitude-card__media">
							<img src="<?php echo esc_url($card['img']); ?>" alt="<?php echo esc_attr($card['alt']); ?>" loading="lazy" />
						</div>
						<div class="attitude-card__overlay"></div>
						<div class="attitude-card__content">
							<h3 class="attitude-card__label"><?php echo esc_html($card['label']); ?></h3>
							<span class="attitude-card__tag"><?php echo esc_html($card['tag']); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 6: WHAT OUR CUSTOMERS HAVE TO SAY (Screenshot 1)
	     Interactive 60fps Carousel
	     ====================================================================== -->
	<?php
	$rev_heading   = !empty($reviews_group['heading']) ? $reviews_group['heading'] : __('WHAT OUR CUSTOMERS HAVE TO SAY', 'vellajous');
	$rev_stat1_val = !empty($reviews_group['stat_1_val']) ? $reviews_group['stat_1_val'] : '10,000+';
	$rev_stat1_lbl = !empty($reviews_group['stat_1_lbl']) ? $reviews_group['stat_1_lbl'] : __('CUSTOMERS', 'vellajous');
	$rev_stat2_val = !empty($reviews_group['stat_2_val']) ? $reviews_group['stat_2_val'] : '★ 4.6/5';
	$rev_stat2_lbl = !empty($reviews_group['stat_2_lbl']) ? $reviews_group['stat_2_lbl'] : "RATING\nBY USERS";

	$avatar_base = $upload_dir['baseurl'] . '/avatars/';

	$reviewers = [];
	if (!empty($reviews_group['reviewers']) && is_array($reviews_group['reviewers'])) {
		foreach ($reviews_group['reviewers'] as $r) {

			$name        = !empty($r['name']) ? $r['name'] : 'Customer';
			$avatar_val  = !empty($r['avatar']) ? $r['avatar'] : '';
			$description = !empty($r['description']) ? $r['description'] : (!empty($r['quote']) ? $r['quote'] : '');
			$reviews_val = !empty($r['reviews']) ? $r['reviews'] : (!empty($r['meta']) ? $r['meta'] : __('★★★★★', 'vellajous'));
			$stars_val   = !empty($r['reviews']) ? $r['reviews'] : '★★★★★';

			$reviewers[] = [
				'name'        => $name,
				'img'         => class_vellajous_acf::get_image_url($avatar_val, $avatar_base . 'avatar1.jpg'),
				'description' => $description,
				'reviews'     => $reviews_val,
				'stars'       => $stars_val,
				// Aliases for compatibility
				'quote'       => $description,
				'author'      => '— ' . $name,
				'meta'        => $reviews_val,
			];
		}
	}

	// Active review to display in initial center card (slot 2 or first)
	$active_idx = count($reviewers) >= 3 ? 2 : 0;
	?>
	<section class="section section--white customer-reviews-interactive" id="testimonials" data-reviews="<?php echo esc_attr(wp_json_encode($reviewers)); ?>">
		<div class="container">
			<h2 class="customer-reviews-interactive__heading"><?php echo esc_html($rev_heading); ?></h2>

			<!-- Interactive Avatar Carousel Track -->
			<div class="avatar-carousel-wrapper">
				<button type="button" class="avatar-nav-arrow" id="avatarPrevBtn" aria-label="<?php esc_attr_e('Previous review', 'vellajous'); ?>">
					<svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"></polyline></svg>
				</button>

				<div class="avatar-track" id="avatarTrack">
					<?php
					$initial_classes = [
						0 => 'pos-left-2 slot-0',
						1 => 'pos-left-1 slot-1',
						2 => 'pos-center slot-2 is-active',
						3 => 'pos-right-1 slot-3',
						4 => 'pos-right-2 slot-4',
					];
					foreach ($reviewers as $idx => $rev) :
						$cls = $initial_classes[$idx] ?? 'pos-hidden';
					?>
						<div class="avatar-bubble <?php echo esc_attr($cls); ?>" data-index="<?php echo esc_attr($idx); ?>" data-slot="<?php echo esc_attr($idx); ?>" title="<?php echo esc_attr($rev['name']); ?>">
							<img src="<?php echo esc_url($rev['img']); ?>" alt="<?php echo esc_attr($rev['name']); ?>" />
						</div>
					<?php endforeach; ?>
				</div>

				<button type="button" class="avatar-nav-arrow" id="avatarNextBtn" aria-label="<?php esc_attr_e('Next review', 'vellajous'); ?>">
					<svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
				</button>
			</div>

			<!-- Dynamic Review Cards Rendered via Foreach Loop -->
			<div class="review-display-cards-wrap" id="reviewDisplayWrap">
				<?php foreach ($reviewers as $idx => $rev) :
					$is_active = ($idx === $active_idx);
				?>
					<div class="review-display-card <?php echo $is_active ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr($idx); ?>" <?php if (!$is_active) echo 'style="display: none;"'; ?>>
						<div class="review-display-card__inner">
							<div class="review-author-name"> <?php echo esc_html($rev['name']); ?></div>
							<div class="review-stars-row"><?php echo esc_html($rev['stars']); ?></div>
							<blockquote class="review-quote-text">
								“<?php echo esc_html(trim($rev['description'], "“”\"\t\n\r\0\x0B")); ?>”
							</blockquote>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Pagination Pill Dots -->
			<div class="review-pagination-dots" id="reviewDots">
				<?php foreach ($reviewers as $idx => $rev) : ?>
					<span class="dot <?php echo ($idx === 2 || ($idx === 0 && count($reviewers) < 3)) ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr($idx); ?>"></span>
				<?php endforeach; ?>
			</div>

			<!-- 2-Part Stats Counter Bar -->
			<div class="attitude-stats-bar customer-reviews-stats-bar">
				<div class="attitude-stat-item">
					<span class="attitude-stat-val"><?php echo esc_html($rev_stat1_val); ?></span>
					<span class="attitude-stat-lbl"><?php echo esc_html($rev_stat1_lbl); ?></span>
				</div>
				<div class="attitude-stat-item">
					<span class="attitude-stat-val"><?php echo esc_html($rev_stat2_val); ?></span>
					<span class="attitude-stat-lbl"><?php echo nl2br(esc_html($rev_stat2_lbl)); ?></span>
				</div>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 7: NEWSLETTER VIP CLUB (Ultra-Luxury Emerald Box)
	     ====================================================================== -->
	<?php
	$news_kicker = !empty($newsletter_group['kicker']) ? $newsletter_group['kicker'] : __('PRIVATE VIP ARCHIVE', 'vellajous');
	$news_title  = !empty($newsletter_group['title'])  ? $newsletter_group['title']  : __('RECEIVE 10% OFF YOUR FIRST BOTTLE', 'vellajous');
	$news_desc   = !empty($newsletter_group['desc'])   ? $newsletter_group['desc']   : __('Be the first to know about private archival releases, limited edition gift sets, and complimentary artisanal samples.', 'vellajous');
	$news_btn_t  = !empty($newsletter_group['btn_text']) ? $newsletter_group['btn_text'] : __('SUBSCRIBE', 'vellajous');
	$news_trust  = !empty($newsletter_group['trust_note']) ? $newsletter_group['trust_note'] : __('No spam. Unsubscribe at any time with one click.', 'vellajous');
	?>
	<section class="section section--white newsletter-section">
		<div class="container">
			<div class="newsletter-box">
				<div class="newsletter-box__decor-circle"></div>
				<span class="newsletter-kicker"><?php echo esc_html($news_kicker); ?></span>
				<h2 class="newsletter-title"><?php echo esc_html($news_title); ?></h2>
				<p class="newsletter-desc">
					<?php echo esc_html($news_desc); ?>
				</p>
				<form class="newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing to Vellajous VIP club!');">
					<div class="newsletter-input-wrap">
						<svg class="newsletter-mail-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
						<input type="email" placeholder="<?php esc_attr_e('Enter your email for exclusive access...', 'vellajous'); ?>" required />
						<button type="submit" class="newsletter-submit-btn">
							<span><?php echo esc_html($news_btn_t); ?></span>
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
						</button>
					</div>
				</form>
				<div class="newsletter-trust-note">
					<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
					<span><?php echo esc_html($news_trust); ?></span>
				</div>
			</div>
		</div>
	</section>

</main><!-- #main -->

<?php
get_footer();
