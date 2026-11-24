<?php

/**
   vellajous of Autoloader classes includes
 *
 * @since      1.0.0
 * @package    vellajous
 * @author     Yudiz Solutions Ltd.
 */
require vellajous_DIR . '/inc/autoloader.php';

/**
  vellajous admin side Hooks included.
 */
class_vellajous_admin::get_instance();

/**
  vellajous enqueues scripts and css files included.
 */
class_vellajous_enqueues::get_instance();

/**
  vellajous Custom Post Types included.
 */
class_vellajous_posttypes::get_instance();

/**
 vellajous Custom Taxonomies included.
 */
class_vellajous_taxonomies::get_instance();

/**
 * vellajous WooCommerce Integration included.
 */
if (class_exists('WooCommerce')) {
    class_vellajous_woocommerce::get_instance();
}

/**
 * vellajous Performance & Speed Engine included.
 */
class_vellajous_performance::get_instance();

/**
 * vellajous ACF Fields & Dynamic Page Sections Integration.
 */
class_vellajous_acf::get_instance();

