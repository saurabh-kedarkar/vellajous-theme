<?php
/**
 * Vellajous ACF (Advanced Custom Fields PRO) Integration
 *
 * Registers structured section-by-section ACF Groups for Home Page, About Page,
 * Theme Settings Options Page, and WooCommerce Products.
 * Uses ACF Repeaters for dynamic multi-item sections (Hero Slider, Attitude Cards, Reviewers, Feature Pillars).
 * Enforces array return format for all image fields.
 *
 * @package vellajous
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

class class_vellajous_acf
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // 1. Register Global Options Page
        add_action('acf/init', [$this, 'register_options_page']);

        // 2. Register Field Groups in Code
        add_action('acf/init', [$this, 'register_acf_field_groups']);

        // 3. Auto-import & sync to database on wp_loaded and admin_init
        add_action('wp_loaded', [$this, 'auto_sync_and_seed_check']);
        add_action('admin_init', [$this, 'auto_import_field_groups_to_db']);

        // 4. Point ACF Local JSON to active theme's acf-json directory
        add_filter('acf/settings/save_json', [$this, 'acf_json_save_point']);
        add_filter('acf/settings/load_json', [$this, 'acf_json_load_point']);
    }

    /**
     * Register ACF PRO Options Page for Global Brand Settings
     */
    public function register_options_page()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title' => __('Vellajous Settings', 'vellajous'),
                'menu_title' => __('Vellajous Settings', 'vellajous'),
                'menu_slug'  => 'vellajous-settings',
                'capability' => 'edit_posts',
                'redirect'   => false,
                'icon_url'   => 'dashicons-awards',
                'position'   => 59,
            ]);
        }
    }

    /**
     * Point ACF JSON save directory to active theme's acf-json folder
     */
    public function acf_json_save_point($path)
    {
        return get_template_directory() . '/acf-json';
    }

    /**
     * Point ACF JSON load directory to active theme's acf-json folder
     */
    public function acf_json_load_point($paths)
    {
        $paths[] = get_template_directory() . '/acf-json';
        return $paths;
    }

    /**
     * Check if database sync and content seeding are needed on wp_loaded
     */
    public function auto_sync_and_seed_check()
    {
        if (!function_exists('acf_import_field_group') || !function_exists('update_field')) {
            return;
        }

        $version = 'vellajous_acf_db_v20_reviews_4fields_clean';
        $synced  = get_option('vellajous_acf_db_synced_version');
        $force   = isset($_GET['sync_vellajous_acf']) || isset($_GET['seed_vellajous_data']);

        if ($synced === $version && !$force) {
            return;
        }

        $this->auto_import_field_groups_to_db($force);
    }

    /**
     * Automatically import field groups to the WordPress database
     * so that they immediately appear in ACF -> Field Groups list.
     */
    public function auto_import_field_groups_to_db($force = false)
    {
        if (!function_exists('acf_import_field_group')) {
            return;
        }

        if (isset($_GET['sync_vellajous_acf']) || isset($_GET['seed_vellajous_data'])) {
            $force = true;
        }

        $version = 'vellajous_acf_db_v20_reviews_4fields_clean';
        $synced  = get_option('vellajous_acf_db_synced_version');

        if ($synced === $version && !$force) {
            return;
        }

        // 1. Ensure Home page has proper Front Page settings & template
        $front_page_id = (int) get_option('page_on_front');
        if ($front_page_id <= 0) {
            $home_page = function_exists('get_page_by_path') ? get_page_by_path('home') : null;
            if (!$home_page && function_exists('get_posts')) {
                $candidates = get_posts([
                    'post_type'      => 'page',
                    'post_status'    => 'any',
                    'posts_per_page' => 1,
                    'title'          => 'Home',
                ]);
                if (!empty($candidates)) {
                    $home_page = $candidates[0];
                }
            }
            if ($home_page) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_page->ID);
                update_post_meta($home_page->ID, '_wp_page_template', 'front-page.php');
            }
        } else {
            // Guarantee page_template is set to front-page.php
            update_post_meta($front_page_id, '_wp_page_template', 'front-page.php');
        }

        // 2. Ensure About page has page-about.php template
        if (function_exists('get_page_by_path')) {
            $about_page = get_page_by_path('about');
            if (!$about_page) {
                $about_page = get_page_by_path('about-us');
            }
            if ($about_page) {
                update_post_meta($about_page->ID, '_wp_page_template', 'page-about.php');
            }
        }

        // 3. Import all field groups with their database ID to force deletion of stale fields
        $groups = [
            self::get_theme_settings_group(),
            self::get_home_page_group(),
            self::get_about_page_group(),
            self::get_product_meta_group(),
        ];

        foreach ($groups as $group) {
            $existing = function_exists('acf_get_raw_field_group') ? acf_get_raw_field_group($group['key']) : null;
            if (!empty($existing) && !empty($existing['ID'])) {
                $group['ID'] = (int) $existing['ID'];
            }
            acf_import_field_group($group);
        }

        // 4. Seed rich luxury content into ACF fields so client sees everything pre-populated
        $this->seed_default_acf_data($force);

        update_option('vellajous_acf_db_synced_version', $version);
    }

    /**
     * Look up existing attachment in Media Library or register it automatically
     */
    public static function get_or_register_attachment($file_path, $title = '')
    {
        if (empty($file_path)) {
            return 0;
        }

        $normalized_path = wp_normalize_path($file_path);
        if (!file_exists($normalized_path)) {
            return 0;
        }

        global $wpdb;
        $filename = basename($normalized_path);

        // Check if attachment exists by post_title or guid filename
        if ($wpdb) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND (guid LIKE %s OR post_name = %s) LIMIT 1",
                '%' . $wpdb->esc_like($filename) . '%',
                sanitize_title(pathinfo($filename, PATHINFO_FILENAME))
            ));

            if (!empty($existing)) {
                return (int) $existing;
            }
        }

        $upload_dir = wp_upload_dir();
        $target_file = $normalized_path;

        // If file is outside uploads, copy to uploads folder so WordPress media handles it properly
        if (strpos($normalized_path, wp_normalize_path($upload_dir['basedir'])) === false) {
            $dest_dir = $upload_dir['basedir'] . '/badges';
            if (!file_exists($dest_dir)) {
                wp_mkdir_p($dest_dir);
            }
            $target_file = $dest_dir . '/' . $filename;
            if (!file_exists($target_file)) {
                @copy($normalized_path, $target_file);
            }
        }

        $filetype = wp_check_filetype($filename, null);
        $guid     = str_replace(wp_normalize_path(ABSPATH), home_url('/'), wp_normalize_path($target_file));

        $attachment = [
            'guid'           => $guid,
            'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
            'post_title'     => !empty($title) ? $title : preg_replace('/\.[^.]+$/', '', $filename),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        if (!function_exists('wp_insert_attachment')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }

        $attach_id = wp_insert_attachment($attachment, $target_file);
        if ($attach_id && !is_wp_error($attach_id)) {
            if (function_exists('wp_generate_attachment_metadata')) {
                $attach_data = wp_generate_attachment_metadata($attach_id, $target_file);
                wp_update_attachment_metadata($attach_id, $attach_data);
            }
            return (int) $attach_id;
        }

        return 0;
    }

    /**
     * Seeds initial luxury content and repeaters into ACF PRO fields
     * so that the WordPress edit screens are immediately pre-populated with real brand data.
     */
    public function seed_default_acf_data($force = false)
    {
        if (!function_exists('update_field')) {
            return;
        }

        $upload_dir = wp_upload_dir();
        $uploads_basedir = $upload_dir['basedir'];
        $theme_dir = get_template_directory();
        $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');

        $get_attachment = function ($path, $title = '') {
            return self::get_or_register_attachment($path, $title);
        };

        // ─────────────────────────────────────────────────────────────
        // 1. GLOBAL THEME SETTINGS OPTIONS PAGE (ASSURANCE PILLARS REPEATER)
        // ─────────────────────────────────────────────────────────────
        $existing_pillars = get_field('assurance_pillars', 'option');
        if (empty($existing_pillars) || $force) {
            $badge1 = $get_attachment($theme_dir . '/assets/images/make-in-india-badge.png', 'MADE IN INDIA');
            $badge2 = $get_attachment($theme_dir . '/assets/images/oil-concentration-badge.png', '30% OIL');
            $badge3 = $get_attachment($theme_dir . '/assets/images/longevity-clock-badge.png', '8Hr+ Longevity');
            $badge4 = $get_attachment($theme_dir . '/assets/images/cruelty-free-badge.png', 'CRUELTY-FREE');

            update_field('assurance_pillars', [
                [
                    'badge_image' => $badge1 ?: ($upload_dir['baseurl'] . '/make-in-india-badge.png'),
                    'title'       => 'MADE IN INDIA',
                    'description' => 'Proudly formulated & handcrafted in India with royal heritage.',
                ],
                [
                    'badge_image' => $badge2 ?: ($upload_dir['baseurl'] . '/oil-concentration-badge.png'),
                    'title'       => '30% OIL',
                    'description' => 'High-density extrait de parfum for rich, opulent projection.',
                ],
                [
                    'badge_image' => $badge3 ?: ($upload_dir['baseurl'] . '/longevity-clock-badge.png'),
                    'title'       => '8Hr+ Longevity',
                    'description' => 'Enduring sillage designed to linger gracefully all day long.',
                ],
                [
                    'badge_image' => $badge4 ?: ($upload_dir['baseurl'] . '/cruelty-free-badge.png'),
                    'title'       => 'CRUELTY-FREE',
                    'description' => '100% ethical, vegan-friendly & certified zero animal testing.',
                ],
            ], 'option');
        }

        // ─────────────────────────────────────────────────────────────
        // 2. HOME PAGE (FRONT PAGE) SEEDING
        // ─────────────────────────────────────────────────────────────
        $home_id = (int) get_option('page_on_front');
        if ($home_id <= 0) {
            $home_p = function_exists('get_page_by_path') ? get_page_by_path('home') : null;
            if ($home_p) {
                $home_id = $home_p->ID;
            }
        }

        if ($home_id > 0) {
            // (a) Hero Section & Slides Repeater
            $existing_hero = get_field('hero_section', $home_id);
            if (empty($existing_hero['slides']) || $force) {
                $slide1_desk = $get_attachment($uploads_basedir . '/2026/10/banner-light-cool-flow.jpg', 'Cool Flow Banner');
                $slide1_mob  = $get_attachment($uploads_basedir . '/2026/10/banner-mobile-cool-flow.jpg', 'Cool Flow Mobile');
                $slide2_desk = $get_attachment($uploads_basedir . '/2026/10/banner-light-made-for-you-v3.jpg', 'Into The Mystique Banner');
                $slide2_mob  = $get_attachment($uploads_basedir . '/2026/10/banner-mobile-mystique.jpg', 'Into The Mystique Mobile');
                $slide3_desk = $get_attachment($uploads_basedir . '/2026/10/banner-light-signature.jpg', 'Signature Atelier Banner');
                $slide3_mob  = $get_attachment($uploads_basedir . '/2026/10/banner-mobile-signature.jpg', 'Signature Atelier Mobile');

                update_field('hero_section', [
                    'banner_type' => 'slider',
                    'slides'      => [
                        [
                            'image'         => $slide1_desk ?: ($upload_dir['baseurl'] . '/2026/10/banner-light-cool-flow.jpg'),
                            'image_mobile'  => $slide1_mob ?: ($upload_dir['baseurl'] . '/2026/10/banner-mobile-cool-flow.jpg'),
                            'tagline'       => 'COOL FLOW · EXTRAIT DE PARFUM',
                            'heading'       => 'MADE FOR YOU',
                            'subtext'       => 'A bold contemporary fragrance statement. Pure crisp sillage and magnetic presence crafted for the modern connoisseur.',
                            'btn_primary'   => 'Shop Now',
                            'btn_link'      => '#best-sellers',
                            'btn_secondary' => 'Explore Collection',
                            'btn_sec_link'  => $shop_url,
                        ],
                        [
                            'image'         => $slide2_desk ?: ($upload_dir['baseurl'] . '/2026/10/banner-light-made-for-you-v3.jpg'),
                            'image_mobile'  => $slide2_mob ?: ($upload_dir['baseurl'] . '/2026/10/banner-mobile-mystique.jpg'),
                            'tagline'       => 'HAUTE PARFUMERIE · DUBAI ATELIER',
                            'heading'       => 'INTO THE MYSTIQUE',
                            'subtext'       => 'Pure water-based aqua parfums and royal concentrated attars formulated to endure all day on your skin.',
                            'btn_primary'   => 'Shop Now',
                            'btn_link'      => '#best-sellers',
                            'btn_secondary' => 'Discover All Scents',
                            'btn_sec_link'  => $shop_url,
                        ],
                        [
                            'image'         => $slide3_desk ?: ($upload_dir['baseurl'] . '/2026/10/banner-light-signature.jpg'),
                            'image_mobile'  => $slide3_mob ?: ($upload_dir['baseurl'] . '/2026/10/banner-mobile-signature.jpg'),
                            'tagline'       => 'NEW CAMPAIGN ',
                            'heading'       => 'SIGNATURE ATELIER',
                            'subtext'       => 'Luxury Extrait de Parfum infused with rare botanical extracts and captivating modern sillage.',
                            'btn_primary'   => 'Shop Now',
                            'btn_link'      => $shop_url,
                            'btn_secondary' => 'View All Scents',
                            'btn_sec_link'  => $shop_url,
                        ],
                    ],
                ], $home_id);
            }

            // (b) Best Sellers Section
            $existing_bs = get_field('best_sellers_section', $home_id);
            if (empty($existing_bs['title']) || $force) {
                update_field('best_sellers_section', [
                    'kicker'      => 'Iconic Fragrances',
                    'title'       => 'BEST SELLERS',
                    'description' => 'Our most acclaimed artisanal masterpieces, celebrated for their captivating presence and exquisite complexity.',
                    'btn_text'    => 'View All',
                    'btn_link'    => $shop_url,
                ], $home_id);
            }

            // (c) Spotlight Section
            $existing_spot = get_field('spotlight_section', $home_id);
            if (empty($existing_spot['title']) || $force) {
                $spot_img = $get_attachment($uploads_basedir . '/2026/10/eclipsenewconcept1.jpg', 'Uniform Masterpiece');
                update_field('spotlight_section', [
                    'prestige_badge'    => 'SIGNATURE MASTERPIECE',
                    'title'             => 'UNIFORM',
                    'concentration_tag' => 'EXTRAIT DE PARFUM · 50ML · 30% OIL',
                    'product_image'     => $spot_img ?: ($upload_dir['baseurl'] . '/2026/10/eclipsenewconcept1.jpg'),
                    'product_id'        => 84,
                    'top_note'          => 'Bergamot',
                    'base_note'         => 'Ambergris',
                    'current_price'     => '₹2,499',
                    'regular_price'     => '₹2,999',
                    'save_badge'        => 'SAVE ₹500',
                    'feature_1_title'   => 'Pure & Gentle:',
                    'feature_1_desc'    => 'Hydrating, skin-loving, and non-sensitizing on delicate skin.',
                    'feature_2_title'   => '8Hr+ Longevity:',
                    'feature_2_desc'    => 'High-density 30% oil concentration for all-day projection.',
                    'btn_text'          => 'Explore Details',
                    'btn_link'          => home_url('/product/uniform/'),
                ], $home_id);
            }

            // (d) Match Your Attitude Section & Cards Repeater
            $existing_att = get_field('attitude_section', $home_id);
            if (empty($existing_att['cards']) || $force) {
                $c1_img = $get_attachment($uploads_basedir . '/attitude/hustler.jpg', 'Hustler');
                $c2_img = $get_attachment($uploads_basedir . '/attitude/gentleman.jpg', 'Gentleman');
                $c3_img = $get_attachment($uploads_basedir . '/attitude/sporty.jpg', 'Sporty');
                $c4_img = $get_attachment($uploads_basedir . '/attitude/adventurer.jpg', 'Adventurer');
                $c5_img = $get_attachment($uploads_basedir . '/attitude/bold.jpg', 'Bold');
                $c6_img = $get_attachment($uploads_basedir . '/attitude/socializer.jpg', 'Socializer');

                update_field('attitude_section', [
                    'kicker' => 'Curated By Personality',
                    'title'  => 'MATCH YOUR ATTITUDE',
                    'cards'  => [
                        [
                            'image' => $c1_img ?: ($upload_dir['baseurl'] . '/attitude/hustler.jpg'),
                            'label' => 'HUSTLER',
                            'tag'   => 'Bold & Focused',
                            'link'  => $shop_url,
                        ],
                        [
                            'image' => $c2_img ?: ($upload_dir['baseurl'] . '/attitude/gentleman.jpg'),
                            'label' => 'GENTLEMAN',
                            'tag'   => 'Classic & Refined',
                            'link'  => $shop_url,
                        ],
                        [
                            'image' => $c3_img ?: ($upload_dir['baseurl'] . '/attitude/sporty.jpg'),
                            'label' => 'SPORTY',
                            'tag'   => 'Fresh & Energetic',
                            'link'  => $shop_url,
                        ],
                        [
                            'image' => $c4_img ?: ($upload_dir['baseurl'] . '/attitude/adventurer.jpg'),
                            'label' => 'ADVENTURER',
                            'tag'   => 'Raw & Mysterious',
                            'link'  => $shop_url,
                        ],
                        [
                            'image' => $c5_img ?: ($upload_dir['baseurl'] . '/attitude/bold.jpg'),
                            'label' => 'BOLD',
                            'tag'   => 'Intense & Charismatic',
                            'link'  => $shop_url,
                        ],
                        [
                            'image' => $c6_img ?: ($upload_dir['baseurl'] . '/attitude/socializer.jpg'),
                            'label' => 'SOCIALIZER',
                            'tag'   => 'Warm & Magnetic',
                            'link'  => $shop_url,
                        ],
                    ],
                ], $home_id);
            }

            // (e) Reviews Section & Reviewers Repeater (Complete Review Per Row)
            $existing_rev = get_field('reviews_section', $home_id);
            if (empty($existing_rev['reviewers']) || empty($existing_rev['reviewers'][0]['description']) || $force) {
                $av1 = $get_attachment($uploads_basedir . '/avatars/avatar1.jpg', 'Aarav M.');
                $av2 = $get_attachment($uploads_basedir . '/avatars/avatar2.jpg', 'Neha D.');
                $av3 = $get_attachment($uploads_basedir . '/avatars/avatar3.jpg', 'Faizan A.');
                $av4 = $get_attachment($uploads_basedir . '/avatars/avatar4.jpg', 'Priya S.');
                $av5 = $get_attachment($uploads_basedir . '/avatars/avatar5.jpg', 'Sneha K.');

                update_field('reviews_section', [
                    'heading'      => 'WHAT OUR CUSTOMERS HAVE TO SAY',
                    'stat_1_val'   => '10,000+',
                    'stat_1_lbl'   => 'CUSTOMERS',
                    'stat_2_val'   => '★ 4.6/5',
                    'stat_2_lbl'   => "RATING\nBY USERS",
                    'reviewers'    => [
                        [
                            'name'        => 'Aarav M.',
                            'avatar'      => $av1 ?: ($upload_dir['baseurl'] . '/avatars/avatar1.jpg'),
                            'description' => 'Ordered ASMIRA for my anniversary. The packaging is pure luxury and the floral aroma lasts through the entire day. Absolutely exquisite luxury perfume.',
                            'reviews'     => 'Verified buyer · ASMIRA Aqua Parfum · Mumbai',
                            'stars'       => '★★★★★',
                            'quote'       => 'Ordered ASMIRA for my anniversary. The packaging is pure luxury and the floral aroma lasts through the entire day. Absolutely exquisite luxury perfume.',
                            'meta'        => 'Verified buyer · ASMIRA Aqua Parfum · Mumbai',
                        ],
                        [
                            'name'        => 'Neha D.',
                            'avatar'      => $av2 ?: ($upload_dir['baseurl'] . '/avatars/avatar2.jpg'),
                            'description' => 'The sillage is incredible. Unlike chemical alcohol perfumes that evaporate quickly, this subtle oil essence warms with your skin. 10/10 recommend!',
                            'reviews'     => 'Verified buyer · Sublime Oud · New Delhi',
                            'stars'       => '★★★★★',
                            'quote'       => 'The sillage is incredible. Unlike chemical alcohol perfumes that evaporate quickly, this subtle oil essence warms with your skin. 10/10 recommend!',
                            'meta'        => 'Verified buyer · Sublime Oud · New Delhi',
                        ],
                        [
                            'name'        => 'Faizan A.',
                            'avatar'      => $av3 ?: ($upload_dir['baseurl'] . '/avatars/avatar3.jpg'),
                            'description' => 'Delivered in three days. The cold marine opening stays crisp right through a working day.',
                            'reviews'     => 'Verified buyer · Aqua Rush · Hyderabad',
                            'stars'       => '★★★★☆',
                            'quote'       => 'Delivered in three days. The cold marine opening stays crisp right through a working day.',
                            'meta'        => 'Verified buyer · Aqua Rush · Hyderabad',
                        ],
                        [
                            'name'        => 'Priya S.',
                            'avatar'      => $av4 ?: ($upload_dir['baseurl'] . '/avatars/avatar4.jpg'),
                            'description' => 'Eclipse is mesmerizing! The smoky amber and orange notes make heads turn every time. Safe on my sensitive skin with zero irritation.',
                            'reviews'     => 'Verified buyer · Eclipse · Pune',
                            'stars'       => '★★★★★',
                            'quote'       => 'Eclipse is mesmerizing! The smoky amber and orange notes make heads turn every time. Safe on my sensitive skin with zero irritation.',
                            'meta'        => 'Verified buyer · Eclipse · Pune',
                        ],
                        [
                            'name'        => 'Sneha K.',
                            'avatar'      => $av5 ?: ($upload_dir['baseurl'] . '/avatars/avatar5.jpg'),
                            'description' => 'Lamsa Gift Set is the best gift I have given to myself. Premium presentation and long-lasting aroma. Fast delivery in just 2 days.',
                            'reviews'     => 'Verified buyer · Lamsa Gift Set · Bengaluru',
                            'stars'       => '★★★★★',
                            'quote'       => 'Lamsa Gift Set is the best gift I have given to myself. Premium presentation and long-lasting aroma. Fast delivery in just 2 days.',
                            'meta'        => 'Verified buyer · Lamsa Gift Set · Bengaluru',
                        ],
                    ],
                ], $home_id);
            }

            // (f) VIP Newsletter Section
            $existing_news = get_field('newsletter_section', $home_id);
            if (empty($existing_news['title']) || $force) {
                update_field('newsletter_section', [
                    'kicker'     => 'PRIVATE VIP ARCHIVE',
                    'title'      => 'RECEIVE 10% OFF YOUR FIRST BOTTLE',
                    'desc'       => 'Be the first to know about private archival releases, limited edition gift sets, and complimentary artisanal samples.',
                    'btn_text'   => 'SUBSCRIBE',
                    'trust_note' => 'No spam. Unsubscribe at any time with one click.',
                ], $home_id);
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 3. ABOUT US PAGE SEEDING
        // ─────────────────────────────────────────────────────────────
        $about_id = 0;
        if (function_exists('get_page_by_path')) {
            $about_p = get_page_by_path('about') ?: get_page_by_path('about-us');
            if ($about_p) {
                $about_id = $about_p->ID;
            }
        }

        if ($about_id > 0) {
            $existing_story = get_field('about_story_section', $about_id);
            if (empty($existing_story['heading']) || $force) {
                $showcase_img = $get_attachment($uploads_basedir . '/2026/10/vellajous-collection-showcase.jpg', 'Vellajous Collection Showcase');

                update_field('about_hero_section', [
                    'title'    => 'ABOUT VELLAJOUS',
                    'subtitle' => 'VELLAJOUS is a modern perfume brand built around one simple thought: fragrance should feel personal. Not limited to Men, Women, or Unisex, but discovered through who you are, the environment you move through, how you feel, and what simply feels right to you.',
                ], $about_id);

                update_field('about_story_section', [
                    'kicker'         => 'THE GENESIS',
                    'heading'        => 'OUR STORY',
                    'lead'           => 'VELLAJOUS began as a simple business idea and slowly became something much more personal.',
                    'text'           => 'While working in interior design, we started exploring perfumes for ourselves — trying different scents, learning about notes, attending workshops and understanding how fragrance fits into different lives. Conversations with people from different professions, lifestyles, and environments made one thing clear: the way we experience fragrance is deeply connected to the world around us. That curiosity became VELLAJOUS.',
                    'showcase_image' => $showcase_img ?: ($upload_dir['baseurl'] . '/2026/10/vellajous-collection-showcase.jpg'),
                ], $about_id);

                update_field('about_mission_vision_section', [
                    'mission_title' => 'OUR MISSION',
                    'mission_desc'  => 'To craft enduring, high-concentration luxury fragrances that harmonize with natural skin chemistry.',
                    'vision_title'  => 'OUR VISION',
                    'vision_desc'   => 'To redefine modern Indian haute perfumerie on the global stage.',
                ], $about_id);

                update_field('about_founder_section', [
                    'quote'        => 'If you’re stepping out, that’s enough. Wear the scent you love. Make it part of getting ready, part of your routine, part of your presence. Don’t Save Your Aura.',
                    'author_name'  => 'Founder & Creative Director',
                    'author_title' => 'Vellajous Parfums',
                ], $about_id);
            }
        }
    }

    /**
     * Safely resolve image URL whether ACF returns an array, ID, or URL string
     */
    public static function get_image_url($img_field, $fallback_url = '')
    {
        if (is_array($img_field) && !empty($img_field['url'])) {
            return $img_field['url'];
        }
        if (is_numeric($img_field) && $img_field > 0) {
            $url = wp_get_attachment_image_url($img_field, 'full');
            if ($url) {
                return $url;
            }
        }
        if (is_string($img_field) && !empty($img_field)) {
            return $img_field;
        }
        return $fallback_url;
    }

    /**
     * Safely resolve image alt text from ACF array
     */
    public static function get_image_alt($img_field, $fallback_alt = 'Vellajous')
    {
        if (is_array($img_field) && !empty($img_field['alt'])) {
            return $img_field['alt'];
        }
        if (is_array($img_field) && !empty($img_field['title'])) {
            return $img_field['title'];
        }
        return $fallback_alt;
    }

    /**
     * Register ACF Local Field Groups with Section Groups and Image Arrays
     */
    public function register_acf_field_groups()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(self::get_theme_settings_group());
        acf_add_local_field_group(self::get_home_page_group());
        acf_add_local_field_group(self::get_about_page_group());
        acf_add_local_field_group(self::get_product_meta_group());
    }

    // ══════════════════════════════════════════════════════════════════
    // 0. GLOBAL THEME SETTINGS OPTIONS GROUP (COMMON ASSURANCE PILLARS)
    // ══════════════════════════════════════════════════════════════════
    public static function get_theme_settings_group()
    {
        return [
            'key'                   => 'group_vellajous_theme_settings',
            'title'                 => __('Global Theme Settings & Common Features (Vellajous)', 'vellajous'),
            'fields'                => [
                [
                    'key'          => 'field_theme_pillars_tab',
                    'label'        => __('Assurance Badges / Features', 'vellajous'),
                    'type'         => 'tab',
                ],
                [
                    'key'          => 'field_theme_assurance_pillars',
                    'label'        => __('Luxury Assurance Pillars (Common Section)', 'vellajous'),
                    'name'         => 'assurance_pillars',
                    'type'         => 'repeater',
                    'instructions' => __('These feature badges appear across Home Page, About Us Page, and all brand pages. Edit here to update everywhere at once.', 'vellajous'),
                    'layout'       => 'block',
                    'button_label' => __('+ Add Feature Badge', 'vellajous'),
                    'min'          => 0,
                    'sub_fields'   => [
                        [
                            'key'           => 'field_theme_pillar_badge',
                            'label'         => __('Badge Icon / Image', 'vellajous'),
                            'name'          => 'badge_image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'thumbnail',
                        ],
                        [
                            'key'           => 'field_theme_pillar_title',
                            'label'         => __('Badge Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'MADE IN INDIA',
                        ],
                        [
                            'key'           => 'field_theme_pillar_desc',
                            'label'         => __('Badge Description', 'vellajous'),
                            'name'          => 'description',
                            'type'          => 'text',
                            'default_value' => 'Proudly formulated & handcrafted in India with royal heritage.',
                        ],
                    ],
                ],
            ],
            'location'              => [
                [
                    [
                        'param'     => 'options_page',
                        'operator'  => '==',
                        'value'     => 'vellajous-settings',
                    ],
                ],
            ],
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    // 1. HOME PAGE (FRONT PAGE) - SECTION-BY-SECTION GROUPS & REPEATERS
    // ══════════════════════════════════════════════════════════════════
    public static function get_home_page_group()
    {
        $location = [
            [
                [
                    'param'     => 'page_type',
                    'operator'  => '==',
                    'value'     => 'front_page',
                ],
            ],
            [
                [
                    'param'     => 'page_template',
                    'operator'  => '==',
                    'value'     => 'front-page.php',
                ],
            ],
        ];

        if (function_exists('get_option')) {
            $front_id = (int) get_option('page_on_front');
            if ($front_id > 0) {
                $location[] = [
                    [
                        'param'    => 'page',
                        'operator' => '==',
                        'value'    => (string) $front_id,
                    ],
                ];
            }
        }

        if (function_exists('get_posts')) {
            $home_posts = get_posts([
                'post_type'      => 'page',
                'post_status'    => 'any',
                'posts_per_page' => 5,
                'title'          => 'Home',
            ]);
            if (!empty($home_posts)) {
                foreach ($home_posts as $hp) {
                    $location[] = [
                        [
                            'param'    => 'page',
                            'operator' => '==',
                            'value'    => (string) $hp->ID,
                        ],
                    ];
                }
            }
        }

        return [
            'key'                   => 'group_vellajous_home_page',
            'title'                 => __('Home Page Settings (Vellajous)', 'vellajous'),
            'fields'                => [

                // ── SECTION 1: HERO MASTER BANNER GROUP ──
                [
                    'key'           => 'field_hero_section',
                    'label'         => __('1. Hero Master Banner Section', 'vellajous'),
                    'name'          => 'hero_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_hero_banner_type',
                            'label'         => __('Banner Display Mode', 'vellajous'),
                            'name'          => 'banner_type',
                            'type'          => 'select',
                            'choices'       => [
                                'slider'    => __('Image Slider Banner', 'vellajous'),
                                'video'     => __('Video Banner', 'vellajous'),
                            ],
                            'default_value' => 'slider',
                        ],

                        // REPEATER for Hero Slides
                        [
                            'key'           => 'field_hero_slides',
                            'label'         => __('Hero Slider Slides (Repeater)', 'vellajous'),
                            'name'          => 'slides',
                            'type'          => 'repeater',
                            'instructions'  => __('Add or manage dynamic slides for the Hero Banner. Drag to reorder.', 'vellajous'),
                            'layout'        => 'block',
                            'button_label'  => __('+ Add Slide', 'vellajous'),
                            'min'           => 1,
                            'sub_fields'    => [
                                [
                                    'key'           => 'field_hero_slide_image',
                                    'label'         => __('Desktop Image (1920x800 recommended)', 'vellajous'),
                                    'name'          => 'image',
                                    'type'          => 'image',
                                    'return_format' => 'array',
                                    'preview_size'  => 'medium',
                                ],
                                [
                                    'key'           => 'field_hero_slide_image_mobile',
                                    'label'         => __('Mobile Image (768x900 recommended)', 'vellajous'),
                                    'name'          => 'image_mobile',
                                    'type'          => 'image',
                                    'return_format' => 'array',
                                    'preview_size'  => 'medium',
                                ],
                                [
                                    'key'           => 'field_hero_slide_tagline',
                                    'label'         => __('Slide Tagline', 'vellajous'),
                                    'name'          => 'tagline',
                                    'type'          => 'text',
                                    'default_value' => 'COOL FLOW · EXTRAIT DE PARFUM',
                                ],
                                [
                                    'key'           => 'field_hero_slide_heading',
                                    'label'         => __('Slide Heading', 'vellajous'),
                                    'name'          => 'heading',
                                    'type'          => 'text',
                                    'default_value' => 'MADE FOR YOU',
                                ],
                                [
                                    'key'           => 'field_hero_slide_subtext',
                                    'label'         => __('Slide Subtext', 'vellajous'),
                                    'name'          => 'subtext',
                                    'type'          => 'textarea',
                                    'rows'          => 2,
                                    'default_value' => 'A bold contemporary fragrance statement. Pure crisp sillage and magnetic presence crafted for the modern connoisseur.',
                                ],
                                [
                                    'key'           => 'field_hero_slide_btn_primary',
                                    'label'         => __('Primary Button Text', 'vellajous'),
                                    'name'          => 'btn_primary',
                                    'type'          => 'text',
                                    'default_value' => 'Shop Now',
                                ],
                                [
                                    'key'           => 'field_hero_slide_btn_link',
                                    'label'         => __('Primary Button Link', 'vellajous'),
                                    'name'          => 'btn_link',
                                    'type'          => 'text',
                                    'default_value' => '#best-sellers',
                                ],
                                [
                                    'key'           => 'field_hero_slide_btn_secondary',
                                    'label'         => __('Secondary Button Text (Optional)', 'vellajous'),
                                    'name'          => 'btn_secondary',
                                    'type'          => 'text',
                                    'default_value' => 'Explore Collection',
                                ],
                                [
                                    'key'           => 'field_hero_slide_btn_sec_link',
                                    'label'         => __('Secondary Button Link (Optional)', 'vellajous'),
                                    'name'          => 'btn_sec_link',
                                    'type'          => 'text',
                                    'default_value' => '/shop',
                                ],
                            ],
                        ],

                        // Video Mode Settings
                        [
                            'key'           => 'field_hero_video_url',
                            'label'         => __('Video URL (For Video Banner Mode)', 'vellajous'),
                            'name'          => 'video_url',
                            'type'          => 'url',
                        ],
                        [
                            'key'           => 'field_hero_video_poster',
                            'label'         => __('Video Poster Image', 'vellajous'),
                            'name'          => 'poster_image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                    ],
                ],

                // ── SECTION 2: BEST SELLERS GROUP ──
                [
                    'key'           => 'field_best_sellers_section',
                    'label'         => __('2. Best Sellers Section', 'vellajous'),
                    'name'          => 'best_sellers_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_bs_kicker',
                            'label'         => __('Eyebrow Kicker', 'vellajous'),
                            'name'          => 'kicker',
                            'type'          => 'text',
                            'default_value' => 'Iconic Fragrances',
                        ],
                        [
                            'key'           => 'field_bs_title',
                            'label'         => __('Section Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'BEST SELLERS',
                        ],
                        [
                            'key'           => 'field_bs_desc',
                            'label'         => __('Section Description', 'vellajous'),
                            'name'          => 'description',
                            'type'          => 'textarea',
                            'rows'          => 2,
                            'default_value' => 'Our most acclaimed artisanal masterpieces, celebrated for their captivating presence and exquisite complexity.',
                        ],
                        [
                            'key'           => 'field_bs_btn_text',
                            'label'         => __('Button Text', 'vellajous'),
                            'name'          => 'btn_text',
                            'type'          => 'text',
                            'default_value' => 'View All',
                        ],
                        [
                            'key'           => 'field_bs_btn_link',
                            'label'         => __('Button Link', 'vellajous'),
                            'name'          => 'btn_link',
                            'type'          => 'text',
                            'default_value' => '/shop',
                        ],
                    ],
                ],

                // ── SECTION 3: BRAND SPOTLIGHT MASTERPIECE GROUP ──
                [
                    'key'           => 'field_spotlight_section',
                    'label'         => __('3. Brand Spotlight Masterpiece Section', 'vellajous'),
                    'name'          => 'spotlight_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_spotlight_badge',
                            'label'         => __('Prestige Pill Badge', 'vellajous'),
                            'name'          => 'prestige_badge',
                            'type'          => 'text',
                            'default_value' => 'SIGNATURE MASTERPIECE',
                        ],
                        [
                            'key'           => 'field_spotlight_img',
                            'label'         => __('Spotlight Product Image', 'vellajous'),
                            'name'          => 'product_image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'           => 'field_spotlight_conc',
                            'label'         => __('Concentration Tag', 'vellajous'),
                            'name'          => 'concentration_tag',
                            'type'          => 'text',
                            'default_value' => 'EXTRAIT DE PARFUM · 50ML · 30% OIL',
                        ],
                        [
                            'key'           => 'field_spotlight_title',
                            'label'         => __('Perfume Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'UNIFORM',
                        ],
                        [
                            'key'           => 'field_spotlight_prod_id',
                            'label'         => __('Wishlist Product ID', 'vellajous'),
                            'name'          => 'product_id',
                            'type'          => 'number',
                            'default_value' => 84,
                        ],
                        [
                            'key'           => 'field_spotlight_top_note',
                            'label'         => __('Top Note', 'vellajous'),
                            'name'          => 'top_note',
                            'type'          => 'text',
                            'default_value' => 'Bergamot',
                        ],
                        [
                            'key'           => 'field_spotlight_base_note',
                            'label'         => __('Base Note', 'vellajous'),
                            'name'          => 'base_note',
                            'type'          => 'text',
                            'default_value' => 'Ambergris',
                        ],
                        [
                            'key'           => 'field_spotlight_current_price',
                            'label'         => __('Current Sale Price', 'vellajous'),
                            'name'          => 'current_price',
                            'type'          => 'text',
                            'default_value' => '₹2,499',
                        ],
                        [
                            'key'           => 'field_spotlight_regular_price',
                            'label'         => __('Regular Original Price', 'vellajous'),
                            'name'          => 'regular_price',
                            'type'          => 'text',
                            'default_value' => '₹2,999',
                        ],
                        [
                            'key'           => 'field_spotlight_save_badge',
                            'label'         => __('Save Amount Badge', 'vellajous'),
                            'name'          => 'save_badge',
                            'type'          => 'text',
                            'default_value' => 'SAVE ₹500',
                        ],
                        [
                            'key'           => 'field_spotlight_f1_title',
                            'label'         => __('Feature 1 Title', 'vellajous'),
                            'name'          => 'feature_1_title',
                            'type'          => 'text',
                            'default_value' => 'Pure & Gentle:',
                        ],
                        [
                            'key'           => 'field_spotlight_f1_desc',
                            'label'         => __('Feature 1 Description', 'vellajous'),
                            'name'          => 'feature_1_desc',
                            'type'          => 'text',
                            'default_value' => 'Hydrating, skin-loving, and non-sensitizing on delicate skin.',
                        ],
                        [
                            'key'           => 'field_spotlight_f2_title',
                            'label'         => __('Feature 2 Title', 'vellajous'),
                            'name'          => 'feature_2_title',
                            'type'          => 'text',
                            'default_value' => '8Hr+ Longevity:',
                        ],
                        [
                            'key'           => 'field_spotlight_f2_desc',
                            'label'         => __('Feature 2 Description', 'vellajous'),
                            'name'          => 'feature_2_desc',
                            'type'          => 'text',
                            'default_value' => 'High-density 30% oil concentration for all-day projection.',
                        ],
                        [
                            'key'           => 'field_spotlight_btn_text',
                            'label'         => __('Button Text', 'vellajous'),
                            'name'          => 'btn_text',
                            'type'          => 'text',
                            'default_value' => 'Explore Details',
                        ],
                        [
                            'key'           => 'field_spotlight_btn_link',
                            'label'         => __('Button Link URL', 'vellajous'),
                            'name'          => 'btn_link',
                            'type'          => 'text',
                            'default_value' => '/product/uniform/',
                        ],
                    ],
                ],

                // ── SECTION 4: MATCH YOUR ATTITUDE GROUP (REPEATER) ──
                [
                    'key'           => 'field_attitude_section',
                    'label'         => __('4. Match Your Attitude Section', 'vellajous'),
                    'name'          => 'attitude_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_att_kicker',
                            'label'         => __('Eyebrow Kicker', 'vellajous'),
                            'name'          => 'kicker',
                            'type'          => 'text',
                            'default_value' => 'Curated By Personality',
                        ],
                        [
                            'key'           => 'field_att_title',
                            'label'         => __('Section Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'MATCH YOUR ATTITUDE',
                        ],
                        // REPEATER for Attitude Cards
                        [
                            'key'           => 'field_att_cards',
                            'label'         => __('Attitude Personality Cards (Repeater)', 'vellajous'),
                            'name'          => 'cards',
                            'type'          => 'repeater',
                            'instructions'  => __('Add, edit or reorder personality attitude cards. Drag to reorder.', 'vellajous'),
                            'layout'        => 'table',
                            'button_label'  => __('+ Add Attitude Card', 'vellajous'),
                            'sub_fields'    => [
                                [
                                    'key'           => 'field_att_card_image',
                                    'label'         => __('Card Image', 'vellajous'),
                                    'name'          => 'image',
                                    'type'          => 'image',
                                    'return_format' => 'array',
                                    'preview_size'  => 'thumbnail',
                                ],
                                [
                                    'key'           => 'field_att_card_label',
                                    'label'         => __('Label (e.g. HUSTLER)', 'vellajous'),
                                    'name'          => 'label',
                                    'type'          => 'text',
                                    'default_value' => 'HUSTLER',
                                ],
                                [
                                    'key'           => 'field_att_card_tag',
                                    'label'         => __('Tag (e.g. Bold & Focused)', 'vellajous'),
                                    'name'          => 'tag',
                                    'type'          => 'text',
                                    'default_value' => 'Bold & Focused',
                                ],
                                [
                                    'key'           => 'field_att_card_link',
                                    'label'         => __('Target Link URL', 'vellajous'),
                                    'name'          => 'link',
                                    'type'          => 'text',
                                    'default_value' => '/shop',
                                ],
                            ],
                        ],
                    ],
                ],

                // ── SECTION 5: CUSTOMER REVIEWS & STATS GROUP (REPEATER) ──
                [
                    'key'           => 'field_reviews_section',
                    'label'         => __('5. Customer Reviews & Stats Section', 'vellajous'),
                    'name'          => 'reviews_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_rev_heading',
                            'label'         => __('Section Heading', 'vellajous'),
                            'name'          => 'heading',
                            'type'          => 'text',
                            'default_value' => 'WHAT OUR CUSTOMERS HAVE TO SAY',
                        ],
                        // REPEATER for Customer Reviews (Complete review per row)
                        [
                            'key'           => 'field_rev_reviewers',
                            'label'         => __('Customer Reviews Carousel (Repeater)', 'vellajous'),
                            'name'          => 'reviewers',
                            'type'          => 'repeater',
                            'instructions'  => __('Manage the customer reviews shown in the interactive 3D carousel. Drag to reorder.', 'vellajous'),
                            'layout'        => 'block',
                            'button_label'  => __('+ Add Review', 'vellajous'),
                            'sub_fields'    => [
                                [
                                    'key'           => 'field_rev_rev_name',
                                    'label'         => __('Reviewer Name', 'vellajous'),
                                    'name'          => 'name',
                                    'type'          => 'text',
                                    'default_value' => 'Aarav M.',
                                    'wrapper'       => ['width' => '50'],
                                ],
                                [
                                    'key'           => 'field_rev_rev_avatar',
                                    'label'         => __('Avatar Image', 'vellajous'),
                                    'name'          => 'avatar',
                                    'type'          => 'image',
                                    'return_format' => 'array',
                                    'preview_size'  => 'thumbnail',
                                    'wrapper'       => ['width' => '50'],
                                ],
                                [
                                    'key'           => 'field_6ae2edd203d19',
                                    'label'         => __('Description', 'vellajous'),
                                    'name'          => 'description',
                                    'type'          => 'textarea',
                                    'rows'          => 3,
                                    'default_value' => 'Ordered ASMIRA for my anniversary. The packaging is pure luxury and the floral aroma lasts through the entire day. Absolutely exquisite luxury perfume.',
                                    'wrapper'       => ['width' => '100'],
                                ],
                                [
                                    'key'           => 'field_6ae2ee36571f6',
                                    'label'         => __('Reviews', 'vellajous'),
                                    'name'          => 'reviews',
                                    'type'          => 'text',
                                    'default_value' => 'Verified buyer · ASMIRA Aqua Parfum · Mumbai',
                                    'instructions'  => __('e.g. Verified buyer · Product Name · City', 'vellajous'),
                                    'wrapper'       => ['width' => '100'],
                                ],
                            ],
                        ],
                        // Stats Counters
                        [
                            'key'           => 'field_rev_stat1_val',
                            'label'         => __('Stat 1 Value', 'vellajous'),
                            'name'          => 'stat_1_val',
                            'type'          => 'text',
                            'default_value' => '10,000+',
                        ],
                        [
                            'key'           => 'field_rev_stat1_lbl',
                            'label'         => __('Stat 1 Label', 'vellajous'),
                            'name'          => 'stat_1_lbl',
                            'type'          => 'text',
                            'default_value' => 'CUSTOMERS',
                        ],
                        [
                            'key'           => 'field_rev_stat2_val',
                            'label'         => __('Stat 2 Value', 'vellajous'),
                            'name'          => 'stat_2_val',
                            'type'          => 'text',
                            'default_value' => '★ 4.6/5',
                        ],
                        [
                            'key'           => 'field_rev_stat2_lbl',
                            'label'         => __('Stat 2 Label', 'vellajous'),
                            'name'          => 'stat_2_lbl',
                            'type'          => 'text',
                            'default_value' => "RATING\nBY USERS",
                        ],
                    ],
                ],

                // ── SECTION 6: VIP NEWSLETTER ARCHIVE GROUP ──
                [
                    'key'           => 'field_newsletter_section',
                    'label'         => __('6. VIP Newsletter Archive Section', 'vellajous'),
                    'name'          => 'newsletter_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_news_kicker',
                            'label'         => __('Eyebrow Kicker', 'vellajous'),
                            'name'          => 'kicker',
                            'type'          => 'text',
                            'default_value' => 'PRIVATE VIP ARCHIVE',
                        ],
                        [
                            'key'           => 'field_news_title',
                            'label'         => __('Newsletter Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'RECEIVE 10% OFF YOUR FIRST BOTTLE',
                        ],
                        [
                            'key'           => 'field_news_desc',
                            'label'         => __('Newsletter Description', 'vellajous'),
                            'name'          => 'desc',
                            'type'          => 'textarea',
                            'rows'          => 2,
                            'default_value' => 'Be the first to know about private archival releases, limited edition gift sets, and complimentary artisanal samples.',
                        ],
                        [
                            'key'           => 'field_news_btn_text',
                            'label'         => __('Submit Button Text', 'vellajous'),
                            'name'          => 'btn_text',
                            'type'          => 'text',
                            'default_value' => 'SUBSCRIBE',
                        ],
                        [
                            'key'           => 'field_news_trust',
                            'label'         => __('Trust Note', 'vellajous'),
                            'name'          => 'trust_note',
                            'type'          => 'text',
                            'default_value' => 'No spam. Unsubscribe at any time with one click.',
                        ],
                    ],
                ],

            ],
            'location'              => $location,
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    // 2. ABOUT US PAGE - SECTION-BY-SECTION GROUPS
    // ══════════════════════════════════════════════════════════════════
    public static function get_about_page_group()
    {
        $location = [
            [
                [
                    'param'     => 'page_template',
                    'operator'  => '==',
                    'value'     => 'page-about.php',
                ],
            ],
        ];

        if (function_exists('get_page_by_path')) {
            $about_p = get_page_by_path('about');
            if (!$about_p) {
                $about_p = get_page_by_path('about-us');
            }
            if ($about_p) {
                $location[] = [
                    [
                        'param'    => 'page',
                        'operator' => '==',
                        'value'    => (string) $about_p->ID,
                    ],
                ];
            }
        }

        return [
            'key'                   => 'group_vellajous_about_page',
            'title'                 => __('About Us Page Settings (Vellajous)', 'vellajous'),
            'fields'                => [
                // Hero Group
                [
                    'key'           => 'field_about_hero_section',
                    'label'         => __('1. About Hero Banner', 'vellajous'),
                    'name'          => 'about_hero_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_about_hero_title',
                            'label'         => __('Hero Title', 'vellajous'),
                            'name'          => 'title',
                            'type'          => 'text',
                            'default_value' => 'ABOUT VELLAJOUS',
                        ],
                        [
                            'key'           => 'field_about_hero_subtitle',
                            'label'         => __('Hero Subtitle', 'vellajous'),
                            'name'          => 'subtitle',
                            'type'          => 'textarea',
                            'rows'          => 2,
                            'default_value' => 'VELLAJOUS is a modern perfume brand built around one simple thought: fragrance should feel personal.',
                        ],
                    ],
                ],

                // Story Group
                [
                    'key'           => 'field_about_story_section',
                    'label'         => __('2. Our Story (The Genesis) Section', 'vellajous'),
                    'name'          => 'about_story_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_about_story_kicker',
                            'label'         => __('Kicker Eyebrow', 'vellajous'),
                            'name'          => 'kicker',
                            'type'          => 'text',
                            'default_value' => 'THE GENESIS',
                        ],
                        [
                            'key'           => 'field_about_story_heading',
                            'label'         => __('Section Heading', 'vellajous'),
                            'name'          => 'heading',
                            'type'          => 'text',
                            'default_value' => 'OUR STORY',
                        ],
                        [
                            'key'           => 'field_about_story_lead',
                            'label'         => __('Story Lead Paragraph', 'vellajous'),
                            'name'          => 'lead',
                            'type'          => 'textarea',
                            'rows'          => 2,
                            'default_value' => 'VELLAJOUS began as a simple business idea and slowly became something much more personal.',
                        ],
                        [
                            'key'           => 'field_about_story_text',
                            'label'         => __('Story Detailed Text', 'vellajous'),
                            'name'          => 'text',
                            'type'          => 'textarea',
                            'rows'          => 4,
                            'default_value' => 'While working in interior design, we started exploring perfumes for ourselves — trying different scents, learning about notes, attending workshops and understanding how fragrance fits into different lives.',
                        ],
                        [
                            'key'           => 'field_about_story_showcase',
                            'label'         => __('Showcase Image', 'vellajous'),
                            'name'          => 'showcase_image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                    ],
                ],

                // Mission & Vision Group
                [
                    'key'           => 'field_about_mission_section',
                    'label'         => __('3. Mission & Vision Section', 'vellajous'),
                    'name'          => 'about_mission_vision_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_about_mission_title',
                            'label'         => __('Mission Title', 'vellajous'),
                            'name'          => 'mission_title',
                            'type'          => 'text',
                            'default_value' => 'OUR MISSION',
                        ],
                        [
                            'key'           => 'field_about_mission_desc',
                            'label'         => __('Mission Description', 'vellajous'),
                            'name'          => 'mission_desc',
                            'type'          => 'textarea',
                            'rows'          => 3,
                            'default_value' => 'To craft enduring, high-concentration luxury fragrances that harmonize with natural skin chemistry.',
                        ],
                        [
                            'key'           => 'field_about_vision_title',
                            'label'         => __('Vision Title', 'vellajous'),
                            'name'          => 'vision_title',
                            'type'          => 'text',
                            'default_value' => 'OUR VISION',
                        ],
                        [
                            'key'           => 'field_about_vision_desc',
                            'label'         => __('Vision Description', 'vellajous'),
                            'name'          => 'vision_desc',
                            'type'          => 'textarea',
                            'rows'          => 3,
                            'default_value' => 'To redefine modern Indian haute perfumerie on the global stage.',
                        ],
                    ],
                ],

                // Founder Note Group
                [
                    'key'           => 'field_about_founder_section',
                    'label'         => __("4. Founder's Note Section", 'vellajous'),
                    'name'          => 'about_founder_section',
                    'type'          => 'group',
                    'layout'        => 'block',
                    'sub_fields'    => [
                        [
                            'key'           => 'field_about_founder_quote',
                            'label'         => __('Founder Quote', 'vellajous'),
                            'name'          => 'quote',
                            'type'          => 'textarea',
                            'rows'          => 3,
                            'default_value' => 'Perfume is not an accessory you put on; it is an aura you leave behind.',
                        ],
                        [
                            'key'           => 'field_about_founder_author',
                            'label'         => __('Author Name', 'vellajous'),
                            'name'          => 'author',
                            'type'          => 'text',
                            'default_value' => 'Saurabh V.',
                        ],
                        [
                            'key'           => 'field_about_founder_title',
                            'label'         => __('Author Role / Title', 'vellajous'),
                            'name'          => 'author_title',
                            'type'          => 'text',
                            'default_value' => 'Founder & Creative Director, Vellajous Parfums',
                        ],
                    ],
                ],
            ],
            'location'              => $location,
            'menu_order'            => 1,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    // 3. WOOCOMMERCE PRODUCT OLFACTORY & BRAND DETAILS
    // ══════════════════════════════════════════════════════════════════
    public static function get_product_meta_group()
    {
        return [
            'key'                   => 'group_vellajous_product_meta',
            'title'                 => __('Perfume Olfactory & Brand Details', 'vellajous'),
            'fields'                => [
                [
                    'key'           => 'field_prod_brand',
                    'label'         => __('Brand Eyebrow', 'vellajous'),
                    'name'          => '_brand',
                    'type'          => 'text',
                    'default_value' => 'VELLAJOUS HAUTE PARFUMERIE',
                    'instructions'  => __('Appears above product title in gold uppercase font.', 'vellajous'),
                ],
                [
                    'key'           => 'field_prod_top_notes',
                    'label'         => __('Top Notes (0-30 Mins Opening)', 'vellajous'),
                    'name'          => 'top_notes',
                    'type'          => 'text',
                    'instructions'  => __('Comma-separated opening chords (e.g. Italian Bergamot, Mandarin, Pear).', 'vellajous'),
                ],
                [
                    'key'           => 'field_prod_heart_notes',
                    'label'         => __('Heart Notes (2-6 Hours Core Aura)', 'vellajous'),
                    'name'          => 'heart_notes',
                    'type'          => 'text',
                    'instructions'  => __('Comma-separated core chords (e.g. Damask Rose, Jasmine, Orange Blossom).', 'vellajous'),
                ],
                [
                    'key'           => 'field_prod_base_notes',
                    'label'         => __('Base Notes (8-12+ Hours Sillage)', 'vellajous'),
                    'name'          => 'base_notes',
                    'type'          => 'text',
                    'instructions'  => __('Comma-separated enduring chords (e.g. Amber Crystal, White Musk, Vanilla).', 'vellajous'),
                ],
            ],
            'location'              => [
                [
                    [
                        'param'     => 'post_type',
                        'operator'  => '==',
                        'value'     => 'product',
                    ],
                ],
            ],
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
        ];
    }
}
