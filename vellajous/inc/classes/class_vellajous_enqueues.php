<?php
if (!defined('ABSPATH')) {
   die;
}

/**
  The enqueue script and style in vellajous
 *
 * @since      1.0.0
 * @package    vellajous
 * @author     Yudiz Solutions Ltd.
 */

if (!class_exists('class_vellajous_enqueues')) {
   class class_vellajous_enqueues
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
       * @return vellajous_enqueue_scripts - Main instance.
       */

      public static function get_instance()
      {
         if (null == self::$instance) {
            self::$instance = new self();
         }
         return self::$instance;
      }

      /**
       * Enqueue scripts and styles.
       * --------********************--------
       */
      public function vellajous_enqueue_scripts()
      {
         // Optimized Google Fonts: Only needed weights with font-display: swap
         wp_enqueue_style(
            'vellajous-google-fonts',
            'https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Montserrat:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap',
            [],
            null
         );

         // Versioning based on file modification times for optimal browser caching
         $main_css_ver = file_exists(vellajous_DIR . '/assets/css/vellajous-main.css') ? filemtime(vellajous_DIR . '/assets/css/vellajous-main.css') : vellajous_VERSION;
         $wc_css_ver   = file_exists(vellajous_DIR . '/assets/css/vellajous-woocommerce.css') ? filemtime(vellajous_DIR . '/assets/css/vellajous-woocommerce.css') : vellajous_VERSION;
         $main_js_ver  = file_exists(vellajous_DIR . '/assets/js/vellajous-main.js') ? filemtime(vellajous_DIR . '/assets/js/vellajous-main.js') : vellajous_VERSION;

         // Main Styles
         wp_enqueue_style('vellajous-style', vellajous_URL . '/style.css', [], vellajous_VERSION);
         wp_enqueue_style('vellajous-main-css', vellajous_URL . '/assets/css/vellajous-main.css', ['vellajous-style'], $main_css_ver);
         wp_enqueue_style('vellajous-wc-css', vellajous_URL . '/assets/css/vellajous-woocommerce.css', ['vellajous-main-css'], $wc_css_ver);

         // Scripts
         wp_enqueue_script('vellajous-navigation', vellajous_URL . '/assets/js/navigation.js', [], vellajous_VERSION, true);
         wp_enqueue_script('vellajous-main-js', vellajous_URL . '/assets/js/vellajous-main.js', ['jquery', 'wc-add-to-cart'], $main_js_ver, true);

         // Localize for AJAX
         wp_localize_script('vellajous-main-js', 'vellajous_data', [
            'ajax_url'    => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('vellajous_ajax_nonce'),
            'cart_url'     => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
            'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
            'wc_ajax_url'  => add_query_arg('wc-ajax', 'add_to_cart', home_url('/')),
         ]);
      }
      // --------********************--------

      /**
        Add Actions/Hooks of enqueue script
       *
       * @since      1.0.0
       * @package    vellajous
       * @author     Yudiz Solutions Ltd.
       * --------********************--------
       */
      public function add_actions()
      {
         add_action('wp_enqueue_scripts', [$this, 'vellajous_enqueue_scripts']);
      }
      // --------********************--------
   }
}
