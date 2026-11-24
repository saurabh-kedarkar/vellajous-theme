<?php
/**
 * Template Name: Wishlist Page
 * Description: Luxury bespoke wishlist page for Vellajous Haute Parfumerie.
 *
 * @package vellajous
 */

get_header();

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
?>

<main id="primary" class="site-main vellajous-wishlist-page">

	<!-- ======================================================================
	     HERO BANNER: WISHLIST
	     ====================================================================== -->
	<section class="vellajous-page-hero vellajous-page-hero--wishlist">
		<div class="vellajous-page-hero__bg">
			<div class="vellajous-page-hero__pattern"></div>
			<div class="vellajous-page-hero__glow"></div>
		</div>
		<div class="container vellajous-page-hero__content">
			<nav class="vellajous-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'vellajous'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a>
				<span class="breadcrumb-separator">/</span>
				<span class="breadcrumb-current"><?php esc_html_e('Wishlist', 'vellajous'); ?></span>
			</nav>

			<h1 class="vellajous-page-hero__title">
				<?php esc_html_e('MY CURATED WISHLIST', 'vellajous'); ?>
			</h1>

			<p class="vellajous-page-hero__subtitle">
				<?php esc_html_e('Your personal sanctuary of saved artisanal fragrances, ready to be unveiled.', 'vellajous'); ?>
			</p>
		</div>
	</section>

	<!-- ======================================================================
	     WISHLIST CONTAINER & DYNAMIC GRID
	     ====================================================================== -->
	<section class="section section--white vellajous-wishlist-section">
		<div class="container">
			<!-- Wishlist Bar: Count & Clear -->
			<div class="vellajous-wishlist-toolbar" id="wishlistToolbar" style="display:none;">
				<div class="wishlist-toolbar__count">
					<span id="wishlistTotalCount">0</span> <?php esc_html_e('Fragrances Saved', 'vellajous'); ?>
				</div>
				<button type="button" class="wishlist-toolbar__clear" id="wishlistClearAll">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
					<?php esc_html_e('Clear All', 'vellajous'); ?>
				</button>
			</div>

			<!-- Loading State -->
			<div class="vellajous-wishlist-loading" id="wishlistLoading">
				<div class="vellajous-spinner"></div>
				<p><?php esc_html_e('Unveiling your private fragrance collection...', 'vellajous'); ?></p>
			</div>

			<!-- Products Grid -->
			<div class="vellajous-products-grid vellajous-wishlist-grid" id="wishlistGrid" style="display:none;">
				<!-- Rendered via AJAX -->
			</div>

			<!-- Empty State -->
			<div class="vellajous-wishlist-empty" id="wishlistEmpty" style="display:none;">
				<div class="wishlist-empty__icon-wrap">
					<svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5">
						<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
					</svg>
				</div>
				<h2 class="wishlist-empty__title"><?php esc_html_e('Your Wishlist is Empty', 'vellajous'); ?></h2>
				<p class="wishlist-empty__desc">
					<?php esc_html_e('You have not saved any perfumes yet. Explore our iconic Best Sellers and handcrafted collections to discover your signature aura.', 'vellajous'); ?>
				</p>
				<a href="<?php echo esc_url($shop_url); ?>" class="vellajous-btn vellajous-btn--primary">
					<?php esc_html_e('Explore Best Sellers', 'vellajous'); ?>
				</a>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
