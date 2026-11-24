<?php
if (!defined('ABSPATH')) {
   die;
}

/**
  The theme support class in vellajous
 *
 * @since      1.0.0
 * @package    vellajous
 */

if (!class_exists("class_vellajous_admin")) {
   class class_vellajous_admin
   {
      /**
       * The single instance of the class.
       */
      private static $instance = null;

      /**
       * Initialize the class and set its properties.
       */
      protected function __construct()
      {
         $this->add_actions();
      }

      /**
        Main Instance.
       *
       * @since 1.0.0
       * @static
       * @return class_vellajous_admin - Main instance.
       */
      public static function get_instance()
      {
         if (null == self::$instance) {
            self::$instance = new self();
         }
         return self::$instance;
      }

      /**
       Admin site theme setup
       * Handle the function vellajous setup
       * Note that this function is hooked into the after_setup_theme hook, which
       * --------********************--------
       */
      public function vellajous_setup()
      {
         /*
  		* Make theme available for translation.
  		* Translations can be filed in the /languages/ directory.
  		*/
         load_theme_textdomain('vellajous', get_template_directory() . '/languages');

         // Add default posts and comments RSS feed links to head.
         add_theme_support('automatic-feed-links');

         /*
  		* Let WordPress manage the document title.
  		* By adding theme support, we declare that this theme does not use a
  		*/
         add_theme_support('title-tag');

         /*
  		* Enable support for Post Thumbnails on posts and pages.
  		*
  		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
  		*/
         add_theme_support('post-thumbnails');

         // Add WooCommerce support
         add_theme_support('woocommerce');
         add_theme_support('wc-product-gallery-zoom');
         add_theme_support('wc-product-gallery-lightbox');
         add_theme_support('wc-product-gallery-slider');

         // Register nav menus
         register_nav_menus(
            array(
               'menu-1' => esc_html__('Primary Menu', 'vellajous'),
               'footer-menu' => esc_html__('Footer Menu', 'vellajous'),
            )
         );

         /*
  		* Switch default core markup for search form, comment form, and comments
  		* to output valid HTML5.
  		*/
         add_theme_support(
            'html5',
            array(
               'search-form',
               'comment-form',
               'comment-list',
               'gallery',
               'caption',
               'style',
               'script',
            )
         );

         // Set up the WordPress core custom background feature.
         add_theme_support(
            'custom-background',
            apply_filters(
               'vellajous_custom_background_args',
               array(
                  'default-color' => 'ffffff',
                  'default-image' => '',
               )
            )
         );

         // Add theme support for selective refresh for widgets.
         add_theme_support('customize-selective-refresh-widgets');

         /**
          * Add support for core custom logo.
          *
          * @link https://codex.wordpress.org/Theme_Logo
          */
         add_theme_support(
            'custom-logo',
            array(
               'height'      => 250,
               'width'       => 250,
               'flex-width'  => true,
               'flex-height' => true,
            )
         );
      }
      // --------********************--------

      /**
      Register widget area.
       * @since 1.0.0
       * @hook action widgets_init
       * --------********************--------
       */
      public function vellajous_widgets_init()
      {
         register_sidebar(
            array(
               'name'          => esc_html__('Sidebar', 'vellajous'),
               'id'            => 'sidebar-1',
               'description'   => esc_html__('Add widgets here.', 'vellajous'),
               'before_widget' => '<section id="%1$s" class="widget %2$s">',
               'after_widget'  => '</section>',
               'before_title'  => '<h2 class="widget-title">',
               'after_title'   => '</h2>',
            )
         );
      }
      // --------********************--------

      /**
      Register ACF options page.
       * @since 1.0.0
       * @param array $items The menu items.
       * --------********************--------
       */
      function vellajous_my_acf_op_init()
      {
         if (function_exists('acf_add_options_page')) {

            $option_page = acf_add_options_page(
               array(
                  'page_title' => __('Theme General Settings'),
                  'menu_title' => __('Theme Settings'),
                  'menu_slug' => 'theme-general-settings',
                  'capability' => 'edit_posts',
                  'redirect' => false
               )
            );
         }
      }
      // --------********************--------

      /**
      Add custom file types to wp_check_filetype_and_ext filter.
       * @since 1.0.0
       * @param array $data The file data.
       * --------********************--------
       */
      function vellajous_filetype_and_ext($data, $file, $filename, $mimes)
      {
         $filetype = wp_check_filetype($filename, $mimes);

         return [
            'ext'             => $filetype['ext'],
            'type'            => $filetype['type'],
            'proper_filename' => $data['proper_filename']
         ];
      }
      // --------********************--------

      /**
      File Type Upload Mimes Start.
       * @since 1.0.0
       * @param array $data The file data.
       * --------********************--------
       */
      function vellajous_cc_mime_types($mimes)
      {
         $mimes['svg'] = 'image/svg+xml';
         return $mimes;
      }
      // --------********************--------
      // --------********************--------
      function my_login_logo()
      { ?>
         <style>
            body.login div#login h1 a {
               background-image: url(<?php echo site_url('/wp-content/uploads/2024/10/Logo-1.svg') ?>);
               background-position: center center;
               background-size: 52% !important;
               width: 320px;
               height: 80px;
               background-size: cover;
            }

            #loginform #wp-submit {
               background-color: #FBAC35;
               border: none;
               border-radius: 3px;
               color: #fff;
               display: inline-block;
               font-size: 16px;
               font-weight: 600;
               padding: 5px 15px 5px 15px;
               text-decoration: none;
               text-transform: uppercase;
               text-shadow: none;
               height: auto;
               line-height: normal;
               box-shadow: none;
            }

            #loginform #wp-submit:hover {
               background-color: #d29e50;
               /* color: #121212; */
               text-decoration: none;
            }

            .login form .forgetmenot label {
               padding-top: 10px;
            }
         </style>
<?php }

      /**
       * Register Theme Customizer settings for Hero Banner.
       * @since 1.0.0
       */
      public function vellajous_customize_register($wp_customize)
      {
         $wp_customize->add_section('vellajous_hero_banner_section', array(
            'title'       => __('Hero Banner (Video & Slider)', 'vellajous'),
            'priority'    => 30,
            'description' => __('Configure whether the homepage hero banner displays the Video or the Photo Slider.', 'vellajous'),
         ));

         $wp_customize->add_setting('vellajous_hero_banner_type', array(
            'default'           => 'slider',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
         ));

         $wp_customize->add_control('vellajous_hero_banner_type', array(
            'label'       => __('Default Banner Media Type', 'vellajous'),
            'description' => __('Choose the default view for the hero banner.', 'vellajous'),
            'section'     => 'vellajous_hero_banner_section',
            'type'        => 'radio',
            'choices'     => array(
               'slider' => __('Photo Slider Banner (High-Res Images)', 'vellajous'),
               'video'  => __('Video Banner (Looping HD Video)', 'vellajous'),
            ),
         ));

         $wp_customize->add_setting('vellajous_hero_video_url', array(
            'default'           => 'https://www.naseemperfume.in/cdn/shop/videos/c/vp/ea98af742d1e4af7af7e1859281b0c7b/ea98af742d1e4af7af7e1859281b0c7b.HD-1080p-7.2Mbps-57379405.mp4?v=0',
            'sanitize_callback' => 'esc_url_raw',
         ));

         $wp_customize->add_control('vellajous_hero_video_url', array(
            'label'   => __('Video Banner URL (MP4)', 'vellajous'),
            'section' => 'vellajous_hero_banner_section',
            'type'    => 'url',
         ));
      }

      /**
       * Add actions.
       * @since 1.0.0
       * --------********************--------
       */
      public function add_actions()
      {
         add_action('after_setup_theme', [$this, 'vellajous_setup']);
         add_action('widgets_init', [$this, 'vellajous_widgets_init']);
         add_action('customize_register', array($this, 'vellajous_customize_register'));
         add_action('acf/init', array($this, 'vellajous_my_acf_op_init'));
         // ------------------------- File Type  -----------------------------
         add_filter('wp_check_filetype_and_ext', array($this, 'vellajous_filetype_and_ext'), 10, 4);
         // ------------------------- File Type Upload Mimes  ----------------
         add_filter('upload_mimes', array($this, 'vellajous_cc_mime_types'));
         // ---------- File login logo  ----------------
         // add_action('login_enqueue_scripts',  array($this, 'my_login_logo'));
      }
      //  --------********************--------

   }
}
