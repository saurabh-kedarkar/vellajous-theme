<?php
/**
 * Template Name: My Account Page
 * Description: Bespoke luxury My Account page template for Vellajous Haute Parfumerie.
 *
 * @package vellajous
 */

get_header();

$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
?>

<main id="primary" class="site-main vellajous-myaccount-page">

	<!-- ======================================================================
	     HERO BANNER
	     ====================================================================== -->
	<section class="vellajous-page-hero vellajous-page-hero--account">
		<div class="vellajous-page-hero__bg">
			<div class="vellajous-page-hero__pattern"></div>
		</div>
		<div class="container vellajous-page-hero__content">
			<nav class="vellajous-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'vellajous'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a>
				<span class="breadcrumb-separator">/</span>
				<span class="breadcrumb-current"><?php esc_html_e('My Account', 'vellajous'); ?></span>
			</nav>

			<?php if ($is_logged_in) : ?>
				<div class="vellajous-page-hero__badge">
					<span class="eyebrow-diamond">♦</span>
					<span><?php esc_html_e('VELLAJOUS PRIVÉ · TIER I PATRON', 'vellajous'); ?></span>
					<span class="eyebrow-diamond">♦</span>
				</div>

				<h1 class="vellajous-page-hero__title">
					<?php 
					/* translators: %s: user display name */
					printf(esc_html__('SALUTATIONS, %s', 'vellajous'), esc_html(strtoupper($current_user->display_name))); 
					?>
				</h1>

				<p class="vellajous-page-hero__subtitle">
					<?php esc_html_e('Welcome to your personal scent chamber. Oversee your acquisitions, manage delivery locations, and consult with our private concierge.', 'vellajous'); ?>
				</p>
			<?php else : ?>
				<div class="vellajous-page-hero__badge">
					<span class="eyebrow-diamond">♦</span>
					<span><?php esc_html_e('MY ACCOUNT · VELLAJOUS', 'vellajous'); ?></span>
					<span class="eyebrow-diamond">♦</span>
				</div>

				<h1 class="vellajous-page-hero__title">
					<?php esc_html_e('MY ACCOUNT', 'vellajous'); ?>
				</h1>

				<p class="vellajous-page-hero__subtitle">
					<?php esc_html_e('Sign in to access your order history, track shipments, and manage your account.', 'vellajous'); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- ======================================================================
	     MY ACCOUNT MAIN CONTENT WRAPPER
	     ====================================================================== -->
	<section class="section section--white vellajous-account-section">
		<div class="container">
			<div class="vellajous-account-wrapper">
				<?php
				while (have_posts()) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     PRE-FOOTER LUXURY ASSURANCE PILLARS
	     ====================================================================== -->
	<section class="vellajous-prefooter-green">
		<div class="container">
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
		</div>
	</section>

</main><!-- #primary -->

<?php
get_footer();
