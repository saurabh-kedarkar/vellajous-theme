<?php
/**
 * The template for displaying the footer
 *
 * @package vellajous
 */

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
?>

<?php 
// Show Green Pre-Footer Assurance Pillars on WooCommerce Shop, Category archives & Single Product pages
if (!is_front_page() && ((function_exists('is_shop') && is_shop()) || (function_exists('is_product_taxonomy') && is_product_taxonomy()) || (function_exists('is_product') && is_product()))) : 
?>
<!-- Pre-Footer: Luxury Green Theme Assurance & VIP Atelier Section (Shop Page Only) -->
<section class="vellajous-prefooter-green">
	<div class="container">
		<?php if (!is_product()) : ?>
		<!-- 4 Luxury Value Pillars (Hidden on Product Pages) -->
		<div class="prefooter-pillars-grid">
			<div class="prefooter-pillar">
				<div class="prefooter-pillar__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
						<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
					</svg>
				</div>
				<div class="prefooter-pillar__content">
					<h4 class="prefooter-pillar__title"><?php esc_html_e('Pure Botanical Formulations', 'vellajous'); ?></h4>
					<p class="prefooter-pillar__text"><?php esc_html_e('Pure water and concentrated botanical essences that respect and hydrate your skin.', 'vellajous'); ?></p>
				</div>
			</div>

			<div class="prefooter-pillar">
				<div class="prefooter-pillar__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
						<circle cx="12" cy="12" r="10"></circle>
						<polyline points="12 6 12 12 16 14"></polyline>
					</svg>
				</div>
				<div class="prefooter-pillar__content">
					<h4 class="prefooter-pillar__title"><?php esc_html_e('12+ Hours Sillage', 'vellajous'); ?></h4>
					<p class="prefooter-pillar__text"><?php esc_html_e('Artisanal Arabian essences delivering deep, enduring projection throughout the day.', 'vellajous'); ?></p>
				</div>
			</div>

			<div class="prefooter-pillar">
				<div class="prefooter-pillar__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
						<rect x="1" y="3" width="15" height="13"></rect>
						<polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
						<circle cx="5.5" cy="18.5" r="2.5"></circle>
						<circle cx="18.5" cy="18.5" r="2.5"></circle>
					</svg>
				</div>
				<div class="prefooter-pillar__content">
					<h4 class="prefooter-pillar__title"><?php esc_html_e('Complimentary Express Courier', 'vellajous'); ?></h4>
					<p class="prefooter-pillar__text"><?php esc_html_e('Fast, insured express shipping direct from our vault across India.', 'vellajous'); ?></p>
				</div>
			</div>

			<div class="prefooter-pillar">
				<div class="prefooter-pillar__icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
						<polyline points="20 12 20 22 4 22 4 12"></polyline>
						<rect x="2" y="7" width="20" height="5"></rect>
						<line x1="12" y1="22" x2="12" y2="7"></line>
						<path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
						<path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
					</svg>
				</div>
				<div class="prefooter-pillar__content">
					<h4 class="prefooter-pillar__title"><?php esc_html_e('Emerald Gift Packaging', 'vellajous'); ?></h4>
					<p class="prefooter-pillar__text"><?php esc_html_e('Every creation arrives nestled in our signature gold-embossed presentation vault.', 'vellajous'); ?></p>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- VIP Newsletter Card (Exact Home Page Form) -->
		<?php
		$front_id         = get_option('page_on_front');
		$newsletter_group = function_exists('get_field') ? get_field('newsletter_section', $front_id) : [];
		$news_kicker      = !empty($newsletter_group['kicker']) ? $newsletter_group['kicker'] : __('PRIVATE VIP ARCHIVE', 'vellajous');
		$news_title       = !empty($newsletter_group['title'])  ? $newsletter_group['title']  : __('RECEIVE 10% OFF YOUR FIRST BOTTLE', 'vellajous');
		$news_desc        = !empty($newsletter_group['desc'])   ? $newsletter_group['desc']   : __('Be the first to know about private archival releases, limited edition gift sets, and complimentary artisanal samples.', 'vellajous');
		$news_btn_t       = !empty($newsletter_group['btn_text']) ? $newsletter_group['btn_text'] : __('SUBSCRIBE', 'vellajous');
		$news_trust       = !empty($newsletter_group['trust_note']) ? $newsletter_group['trust_note'] : __('No spam. Unsubscribe at any time with one click.', 'vellajous');
		?>
		<div class="newsletter-box vellajous-prefooter-newsletter">
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
<?php endif; ?>

<footer id="colophon" class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<!-- Col 1: Brand Info -->
			<div class="footer-col footer-brand">
				<div class="footer-brand__logo">VELLAJOUS</div>
				<p class="footer-brand__desc">
					<?php esc_html_e("It's not just a perfume; it's an Aura", 'vellajous'); ?>
				</p>
				<div class="footer-socials">
					<a href="#" aria-label="Instagram">
						<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
					</a>
					<a href="#" aria-label="Facebook">
						<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
					</a>
					<a href="#" aria-label="YouTube">
						<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
					</a>
				</div>
			</div>

			<!-- Col 2: Navigation -->
			<div class="footer-col footer-col--explore">
				<h4><?php esc_html_e('Explore', 'vellajous'); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a></li>
					<li><a href="<?php echo esc_url(home_url('/about-us/')); ?>"><?php esc_html_e('About Us', 'vellajous'); ?></a></li>
					<li><a href="<?php echo esc_url(home_url('/#best-sellers')); ?>"><?php esc_html_e('Best Sellers', 'vellajous'); ?></a></li>
					<li><a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('All Perfumes', 'vellajous'); ?></a></li>
				</ul>
			</div>

			<!-- Col 3: Contact -->
			<div class="footer-col footer-col--contact">
				<h4><?php esc_html_e("LET'S CONNECT", 'vellajous'); ?></h4>
				<ul>
					<!-- <li><span style="color:#a4b3aa;"><?php esc_html_e('Mon - Sat: 10:00 AM - 7:00 PM', 'vellajous'); ?></span></li> -->
					<li><span style="color:#83948a;"><?php esc_html_e('Jatpura Ward, Chandrapur 442401', 'vellajous'); ?></span></li>
					<li><a href="mailto:info@vellajous.com">info@vellajous.com</a></li>
					<li><a href="tel:+919270201164">+91 92702 01164</a></li>
				</ul>
			</div>
		</div>

		<!-- Bottom Legal & Payments -->
		<div class="footer-bottom">
			<div class="footer-legal-links">
				<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'vellajous'); ?></a>
				<span class="footer-legal-sep">&bull;</span>
				<a href="<?php echo esc_url(home_url('/terms-and-conditions/')); ?>"><?php esc_html_e('Terms & Conditions', 'vellajous'); ?></a>
			</div>
			<div class="footer-copy">
				&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All Rights Reserved', 'vellajous'); ?>
			</div>
			<!-- <div class="footer-payment-badges">
				<span>UPI</span> &nbsp;|&nbsp; <span>VISA</span> &nbsp;|&nbsp; <span>MASTERCARD</span> &nbsp;|&nbsp; <span>NETBANKING</span>
			</div> -->
		</div>
	</div>
</footer>

<!-- Quick View Modal Container -->
<div class="vellajous-modal-backdrop" id="vellajousQuickViewModal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Product Quick View', 'vellajous'); ?>">
	<div class="vellajous-modal-container">
		<!-- Injected via AJAX -->
	</div>
</div>

<!-- Slide-in Mini Cart Drawer Container -->
<div class="vellajous-drawer-backdrop" id="vellajousCartDrawerBackdrop">
	<div class="vellajous-cart-drawer">
		<div class="vellajous-cart-drawer__header">
			<h3><?php esc_html_e('Shopping Bag', 'vellajous'); ?></h3>
			<button type="button" class="vellajous-drawer-close" aria-label="<?php esc_attr_e('Close drawer', 'vellajous'); ?>">&times;</button>
		</div>
		<div class="vellajous-cart-drawer__body">
			<?php
			if (class_exists('class_vellajous_woocommerce')) {
				class_vellajous_woocommerce::get_instance()->render_drawer_cart_content();
			}
			?>
		</div>
	</div>
</div>

<!-- Custom Luxury Brand Cursor -->
<div class="vellajous-cursor" id="vellajousCursor" aria-hidden="true">
	<div class="vellajous-cursor__circle" id="vellajousCursorCircle"></div>
	<div class="vellajous-cursor__dot" id="vellajousCursorDot"></div>
</div>

<!-- Mobile Sticky Bottom Navigation (Screenshot Match) -->
<?php
$b_cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$b_shop_url   = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
$b_account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account');
$b_wishlist_url = home_url('/wishlist/');
?>
<nav class="vellajous-bottom-nav" aria-label="<?php esc_attr_e('Mobile Navigation Bar', 'vellajous'); ?>">
	<!-- 1. Shop -->
	<a href="<?php echo esc_url($b_shop_url); ?>" class="bottom-nav-item <?php echo (is_shop() || is_product_taxonomy()) ? 'is-active' : ''; ?>">
		<span class="bottom-nav-icon">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
				<rect x="3" y="3" width="7" height="7" rx="2"></rect>
				<rect x="14" y="3" width="7" height="7" rx="2"></rect>
				<rect x="14" y="14" width="7" height="7" rx="2"></rect>
				<rect x="3" y="14" width="7" height="7" rx="2"></rect>
			</svg>
		</span>
		<span class="bottom-nav-label"><?php esc_html_e('Shop', 'vellajous'); ?></span>
	</a>

	<!-- 2. Search -->
	<button type="button" class="bottom-nav-item bottom-nav-search-btn" id="bottomNavSearchBtn" aria-label="<?php esc_attr_e('Search', 'vellajous'); ?>">
		<span class="bottom-nav-icon">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
				<circle cx="11" cy="11" r="7"></circle>
				<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
			</svg>
		</span>
		<span class="bottom-nav-label"><?php esc_html_e('Search', 'vellajous'); ?></span>
	</button>

	<!-- 3. Wishlist -->
	<a href="<?php echo esc_url($b_wishlist_url); ?>" class="bottom-nav-item bottom-nav-wishlist-btn <?php echo (is_page('wishlist') || is_page_template('page-wishlist.php')) ? 'is-active' : ''; ?>">
		<span class="bottom-nav-icon has-badge">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
				<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
			</svg>
			<span class="bottom-nav-badge vellajous-wishlist-count" style="display:none;">0</span>
		</span>
		<span class="bottom-nav-label"><?php esc_html_e('Wishlist', 'vellajous'); ?></span>
	</a>
	
	<!-- 4. Account -->
	<a href="<?php echo esc_url($b_account_url); ?>" class="bottom-nav-item <?php echo is_account_page() ? 'is-active' : ''; ?>">
		<span class="bottom-nav-icon">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
				<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
				<circle cx="12" cy="7" r="4"></circle>
			</svg>
		</span>
		<span class="bottom-nav-label"><?php esc_html_e('Account', 'vellajous'); ?></span>
	</a>
</nav>

<!-- Mobile Slide-Down Search Overlay -->
<div class="vellajous-search-overlay" id="vellajousSearchOverlay">
	<div class="vellajous-search-overlay__inner">
		<form role="search" method="get" class="vellajous-search-form" action="<?php echo esc_url(home_url('/')); ?>">
			<input type="hidden" name="post_type" value="product" />
			<div class="search-input-wrap">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<circle cx="11" cy="11" r="7"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
				<input type="search" class="search-field" placeholder="<?php esc_attr_e('Search luxury perfumes...', 'vellajous'); ?>" value="<?php echo get_search_query(); ?>" name="s" id="mobileSearchInput" autocomplete="off" />
				<button type="button" class="search-close-btn" id="searchCloseBtn" aria-label="<?php esc_attr_e('Close search', 'vellajous'); ?>">&times;</button>
			</div>
		</form>
	</div>
</div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>