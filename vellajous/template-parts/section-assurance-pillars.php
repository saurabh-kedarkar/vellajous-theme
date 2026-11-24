<?php
/**
 * Template part for displaying the Luxury Assurance Pillars (Feature Cards)
 *
 * Common reusable section for Front Page, About Us Page, and other touchpoints.
 * Powered by ACF Theme Settings Options Page Repeater with luxury default fallbacks.
 *
 * @package vellajous
 */

$theme_pillars = function_exists('get_field') ? get_field('assurance_pillars', 'option') : null;

// Graceful luxury fallback if not yet configured in ACF Options
$default_pillars = [
	[
		'badge_image' => get_template_directory_uri() . '/assets/images/make-in-india-badge.png',
		'title'       => __('MADE IN INDIA', 'vellajous'),
		'description' => __('Proudly formulated & handcrafted in India with royal heritage.', 'vellajous'),
	],
	[
		'badge_image' => get_template_directory_uri() . '/assets/images/cruelty-free-badge.png',
		'title'       => __('CRUELTY-FREE', 'vellajous'),
		'description' => __('100% ethical, vegan-friendly & certified zero animal testing.', 'vellajous'),
	],
	[
		'badge_image' => get_template_directory_uri() . '/assets/images/oil-concentration-badge.png',
		'title'       => __('30% OIL', 'vellajous'),
		'description' => __('High-density extrait de parfum for rich, opulent projection.', 'vellajous'),
	],
	[
		'badge_image' => get_template_directory_uri() . '/assets/images/longevity-clock-badge.png',
		'title'       => __('8Hr+ Longevity', 'vellajous'),
		'description' => __('Enduring sillage designed to linger gracefully all day long.', 'vellajous'),
	],
];

$pillars_list = (!empty($theme_pillars) && is_array($theme_pillars)) ? $theme_pillars : $default_pillars;
?>

<!-- ======================================================================
     LUXURY PILLARS / ASSURANCE CARDS (Global Common Section)
     ====================================================================== -->
<section class="section vellajous-feature-cards-section vellajous-feature-cards-section--green">
	<div class="container">
		<div class="vellajous-feature-cards__grid">
			<?php foreach ($pillars_list as $pillar) : 
				$badge_img = class_vellajous_acf::get_image_url($pillar['badge_image'] ?? '', get_template_directory_uri() . '/assets/images/make-in-india-badge.png');
				$title     = !empty($pillar['title']) ? $pillar['title'] : '';
				$desc      = !empty($pillar['description']) ? $pillar['description'] : '';
			?>
				<div class="vellajous-feature-card">
					<div class="feature-card__icon-badge feature-card__icon-badge--mint">
						<img src="<?php echo esc_url($badge_img); ?>" alt="<?php echo esc_attr($title); ?>" class="feature-card__badge-img" width="48" height="48" />
					</div>
					<div class="feature-card__content">
						<h4 class="feature-card__title"><?php echo esc_html($title); ?></h4>
						<p class="feature-card__desc"><?php echo esc_html($desc); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
