<?php

/**
 * vellajous functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package vellajous
 */

if (!defined('vellajous_TEXT_DOMAIN')) {
	define('vellajous_TEXT_DOMAIN', 'vellajous');
}

// Replace the version number of the theme on each release.
if (! defined('vellajous_VERSION')) {
	define('vellajous_VERSION', '1.0.0');
}

// Defined theme URL
if (!defined('vellajous_URL')) {
	define('vellajous_URL', get_template_directory_uri());
}
// Defined theme DIR
if (!defined('vellajous_DIR')) {
	define('vellajous_DIR', get_template_directory());
}


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * vellajous of Classes Instance Called.
 */
require vellajous_DIR . '/inc/classes_instance_called.php';
