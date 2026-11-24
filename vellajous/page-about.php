<?php
/**
 * Template Name: About Us Page
 * Description: Bespoke haute-parfumerie luxury About Us page for Vellajous.
 *
 * @package vellajous
 */

get_header();

/**
 * Retrieve ACF Section Groups for About Page
 */
$about_hero    = function_exists('get_field') ? get_field('about_hero_section') : null;
$about_story   = function_exists('get_field') ? get_field('about_story_section') : null;
$about_mv      = function_exists('get_field') ? get_field('about_mission_vision_section') : null;
$about_founder = function_exists('get_field') ? get_field('about_founder_section') : null;

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
$contact_url = home_url('/contact-us/');
$upload_dir = wp_upload_dir();

// Dynamic Content with graceful fallbacks
$hero_title    = !empty($about_hero['title']) ? $about_hero['title'] : __('ABOUT VELLAJOUS', 'vellajous');
$hero_subtitle = !empty($about_hero['subtitle']) ? $about_hero['subtitle'] : __('VELLAJOUS is a modern perfume brand built around one simple thought: fragrance should feel personal. Not limited to Men, Women, or Unisex, but discovered through who you are, the environment you move through, how you feel, and what simply feels right to you.', 'vellajous');

$story_kicker  = !empty($about_story['kicker']) ? $about_story['kicker'] : __('THE GENESIS', 'vellajous');
$story_heading = !empty($about_story['heading']) ? $about_story['heading'] : __('OUR STORY', 'vellajous');
$story_lead    = !empty($about_story['lead']) ? $about_story['lead'] : __('VELLAJOUS began as a simple business idea and slowly became something much more personal.', 'vellajous');
$story_text    = !empty($about_story['text']) ? $about_story['text'] : __('While working in interior design, we started exploring perfumes for ourselves — trying different scents, learning about notes, attending workshops and understanding how fragrance fits into different lives. Conversations with people from different professions, lifestyles, and environments made one thing clear: the way we experience fragrance is deeply connected to the world around us. That curiosity became VELLAJOUS.', 'vellajous');
$showcase_img  = !empty($about_story['showcase_image']) ? class_vellajous_acf::get_image_url($about_story['showcase_image']) : ($upload_dir['baseurl'] . '/2026/10/vellajous-collection-showcase.jpg');

$mission_title = !empty($about_mv['mission_title']) ? $about_mv['mission_title'] : __('OUR MISSION', 'vellajous');
$vision_title  = !empty($about_mv['vision_title']) ? $about_mv['vision_title'] : __('OUR VISION', 'vellajous');

$founder_quote = !empty($about_founder['quote']) ? $about_founder['quote'] : __('If you’re stepping out, that’s enough. Wear the scent you love. Make it part of getting ready, part of your routine, part of your presence. Don’t Save Your Aura.', 'vellajous');
?>

<main id="primary" class="site-main vellajous-about-page">

	<!-- ======================================================================
	     HERO BANNER: ABOUT VELLAJOUS
	     ====================================================================== -->
	<section class="vellajous-page-hero vellajous-page-hero--about">
		<div class="vellajous-page-hero__bg">
			<div class="vellajous-page-hero__pattern"></div>
			<div class="vellajous-page-hero__glow"></div>
		</div>
		<div class="container vellajous-page-hero__content">
			<nav class="vellajous-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'vellajous'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a>
				<span class="breadcrumb-separator">/</span>
				<span class="breadcrumb-current"><?php esc_html_e('About Us', 'vellajous'); ?></span>
			</nav>

			<h1 class="vellajous-page-hero__title">
				<?php echo esc_html($hero_title); ?>
			</h1>

			<p class="vellajous-page-hero__subtitle">
				<?php echo esc_html($hero_subtitle); ?>
			</p>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 1: OUR STORY (The Genesis)
	     ====================================================================== -->
	<section class="section section--white vellajous-story-section">
		<div class="container">
			<div class="vellajous-story-grid">
				<!-- Left: Story Narrative -->
				<div class="vellajous-story-narrative">
					<span class="section-header__kicker"><?php echo esc_html($story_kicker); ?></span>
					<h2 class="vellajous-story__heading">
						<?php echo esc_html($story_heading); ?>
					</h2>

					<p class="vellajous-story__lead">
						<?php echo esc_html($story_lead); ?>
					</p>

					<p class="vellajous-story__text">
						<?php echo nl2br(esc_html($story_text)); ?>
					</p>
				</div>

				<!-- Right: Luxury Visual Showcase Card -->
				<div class="vellajous-story-visual">
					<div class="luxury-showcase-card">
						<div class="showcase-card__image-wrap">
							<img src="<?php echo esc_url($showcase_img); ?>" alt="<?php esc_attr_e('Vellajous Haute Parfumerie Collection', 'vellajous'); ?>" class="showcase-card__img" loading="lazy" />
							<div class="showcase-card__badge showcase-card__badge--top">
								<span class="eyebrow-diamond">♦</span>
								<span><?php esc_html_e('Artisanal Formulations', 'vellajous'); ?></span>
							</div>
							<div class="showcase-card__badge showcase-card__badge--bottom">
								<span class="eyebrow-diamond">✦</span>
								<span><?php esc_html_e('12+ Hours Sillage', 'vellajous'); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 2: OUR MISSION & OUR VISION
	     ====================================================================== -->
	<section class="section section--alt vellajous-mission-vision-section">
		<div class="container">	

			<div class="vellajous-mission-vision-grid">
				<!-- Card 1: OUR MISSION -->
				<div class="mission-vision-card mission-card">
					<div class="mission-vision-card__header">
						<div class="mission-vision-card__icon">
							<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
								<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
							</svg>
						</div>
						<div class="mission-vision-card__title-wrap">
							<span class="card-kicker"><?php esc_html_e('OUR PURPOSE', 'vellajous'); ?></span>
							<h3 class="mission-vision-card__title"><?php echo esc_html($mission_title); ?></h3>
						</div>
					</div>
					<ul class="mission-vision-card__list">
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Move perfume discovery beyond Men, Women & Unisex.', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Connect fragrance with real environments and lifestyles.', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Make choosing a perfume more personal and intuitive.', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Help people find scents that feel right for their world.', 'vellajous'); ?></span>
						</li>
					</ul>
				</div>

				<!-- Card 2: OUR VISION -->
				<div class="mission-vision-card vision-card">
					<div class="mission-vision-card__header">
						<div class="mission-vision-card__icon">
							<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
								<circle cx="12" cy="12" r="10"></circle>
								<circle cx="12" cy="12" r="6"></circle>
								<circle cx="12" cy="12" r="2"></circle>
							</svg>
						</div>
						<div class="mission-vision-card__title-wrap">
							<span class="card-kicker"><?php esc_html_e('THE FUTURE', 'vellajous'); ?></span>
							<h3 class="mission-vision-card__title"><?php echo esc_html($vision_title); ?></h3>
						</div>
					</div>
					<ul class="mission-vision-card__list">
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Change the way people discover and choose fragrance.', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Make environment an important part of fragrance identity.', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Move from “Who is this perfume for?” to “Where does it belong in my life?”', 'vellajous'); ?></span>
						</li>
						<li>
							<span class="list-bullet">♦</span>
							<span class="list-text"><?php esc_html_e('Create a fragrance culture that feels more personal, contextual, and human.', 'vellajous'); ?></span>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 3: THE VELLAJOUS WAY
	     ====================================================================== -->
		 <?php /* ?>
	<section class="section section--white vellajous-way-section">
		<div class="container">
			<div class="section-header text-center">
				<span class="section-header__kicker"><?php esc_html_e('DISCOVERY REDEFINED', 'vellajous'); ?></span>
				<h2 class="section-header__title"><?php esc_html_e('THE VELLAJOUS WAY', 'vellajous'); ?></h2>
				<p class="section-header__desc">
					<?php esc_html_e('We believe there is more than one way to find the right scent.', 'vellajous'); ?>
				</p>
			</div>

			<div class="vellajous-way-grid">
				<!-- Way 1: WHO ARE YOU? -->
				<div class="way-card">
					<div class="way-card__badge">01</div>
					<div class="way-card__icon">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
							<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
							<circle cx="12" cy="7" r="4"></circle>
						</svg>
					</div>
					<div class="way-card__tag"><?php esc_html_e('Profession · Lifestyle · Environment', 'vellajous'); ?></div>
					<h3 class="way-card__title"><?php esc_html_e('WHO ARE YOU?', 'vellajous'); ?></h3>
					<p class="way-card__text">
						<?php esc_html_e('Explores fragrance through your profession, lifestyle, and environment. Crafted to harmonize with where you live, work, and move.', 'vellajous'); ?>
					</p>
					<a href="<?php echo esc_url($shop_url); ?>" class="way-card__link">
						<span><?php esc_html_e('Explore By Identity', 'vellajous'); ?></span>
						<span class="arrow">→</span>
					</a>
				</div>

				<!-- Way 2: HOW DO YOU FEEL? -->
				<div class="way-card">
					<div class="way-card__badge">02</div>
					<div class="way-card__icon">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
							<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
						</svg>
					</div>
					<div class="way-card__tag"><?php esc_html_e('Mood · Energy · Atmosphere', 'vellajous'); ?></div>
					<h3 class="way-card__title"><?php esc_html_e('HOW DO YOU FEEL?', 'vellajous'); ?></h3>
					<p class="way-card__text">
						<?php esc_html_e('Connects scent with the mood or moment you are stepping into. An ambient aroma designed to elevate your inner spirit and presence.', 'vellajous'); ?>
					</p>
					<a href="<?php echo esc_url($shop_url); ?>" class="way-card__link">
						<span><?php esc_html_e('Explore By Mood', 'vellajous'); ?></span>
						<span class="arrow">→</span>
					</a>
				</div>
			</div>
		</div>
	</section>
<?php */ ?>
	<!-- ======================================================================
	     SECTION 4: DON’T SAVE YOUR AURA (Dark Luxury Feature Banner)
	     ====================================================================== -->
	<section class="vellajous-aura-section">
		<div class="container">
			<div class="vellajous-aura-box">
				<div class="vellajous-aura-content">
					<span class="section-header__kicker kicker-gold"><?php esc_html_e('EVERYDAY ELEVATION', 'vellajous'); ?></span>
					<h2 class="vellajous-aura__title">
						<?php esc_html_e('DON’T SAVE YOUR AURA', 'vellajous'); ?>
					</h2>
					<div class="vellajous-aura__divider"><span>♦</span></div>
					<p class="vellajous-aura__intro">
						<?php esc_html_e('Perfume is often saved for weddings, dates, parties, and special occasions. We see it differently.', 'vellajous'); ?>
					</p>
					<div class="vellajous-aura__highlight">
						<p class="aura-quote">
							“<?php echo esc_html($founder_quote); ?>”
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 5: OUR MOOD PHILOSOPHY & THE VELLAJOUS BELIEF
	     ====================================================================== -->
	<section class="section section--white vellajous-philosophy-section">
		<div class="container">
			<div class="vellajous-philosophy-grid">
				<!-- Left: OUR MOOD PHILOSOPHY -->
				<div class="philosophy-block">
					<span class="section-header__kicker"><?php esc_html_e('GROUNDED & HONEST', 'vellajous'); ?></span>
					<h3 class="philosophy-block__title"><?php esc_html_e('OUR MOOD PHILOSOPHY', 'vellajous'); ?></h3>
					<div class="philosophy-block__divider"></div>
					<p class="philosophy-block__text">
						<?php esc_html_e('A fragrance cannot promise to change how you feel — and we don\'t claim that it can. But the right scent can complement a moment, shape an atmosphere and become part of a personal ritual. That is where we see the connection between fragrance and mood.', 'vellajous'); ?>
					</p>
				</div>

				<!-- Right: THE VELLAJOUS BELIEF -->
				<div class="philosophy-block philosophy-block--belief">
					<span class="section-header__kicker kicker-gold"><?php esc_html_e('OUR CORE CREED', 'vellajous'); ?></span>
					<h3 class="philosophy-block__title philosophy-block__title_sec"><?php esc_html_e('THE VELLAJOUS BELIEF', 'vellajous'); ?></h3>
					<div class="philosophy-block__divider"></div>
					<div class="belief-statement">
						<p class="belief-line belief-line--top">
							<?php esc_html_e('We don\'t believe there is one right way to wear perfume.', 'vellajous'); ?>
						</p>
						<p class="belief-line belief-line--highlight">
							“<?php esc_html_e('We believe there is a scent that feels right for your world.', 'vellajous'); ?>”
						</p>
					</div>
					<div class="philosophy-cta">
						<a href="<?php echo esc_url($shop_url); ?>" class="vellajous-btn vellajous-btn--primary">
							<span><?php esc_html_e('Discover Your Fragrance', 'vellajous'); ?></span>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
						</a>
						<a href="<?php echo esc_url($contact_url); ?>" class="vellajous-btn vellajous-btn--outline">
							<span><?php esc_html_e('Get In Touch', 'vellajous'); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ======================================================================
	     PRE-FOOTER LUXURY ASSURANCE PILLARS (Common Reusable Section)
	     ====================================================================== -->
	<?php get_template_part('template-parts/section', 'assurance-pillars'); ?>

</main><!-- #primary -->

<?php
get_footer();
