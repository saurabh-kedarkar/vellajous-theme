<?php
/**
 * Vellajous Speed and Performance Optimization Engine
 *
 * Streamlines asset loading, cleans up WordPress overhead,
 * enables lazy-loading, and eliminates render-blocking bloat.
 *
 * @package vellajous
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('class_vellajous_performance')) {
    class class_vellajous_performance
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        protected function __construct()
        {
            $this->init_optimizations();
        }

        private function init_optimizations()
        {
            // 1. Remove WordPress Emoji bloat (scripts, styles, and TinyMCE plugins)
            add_action('init', [$this, 'disable_emojis']);

            // 2. Clean head tags (generator, manifest, rsd, shortlinks)
            add_action('init', [$this, 'clean_head_tags']);

            // 3. Dequeue unnecessary Gutenberg block styles on frontend
            add_action('wp_enqueue_scripts', [$this, 'dequeue_unneeded_styles'], 100);

            // 4. Disable XML-RPC (Security + Performance)
            add_filter('xmlrpc_enabled', '__return_false');
            add_filter('wp_headers', [$this, 'remove_x_pingback']);

            // 5. Disable self-pingbacks
            add_action('pre_ping', [$this, 'disable_self_pingbacks']);

            // 6. Slow down Heartbeat API on frontend
            add_action('init', [$this, 'optimize_heartbeat']);

            // 7. Add defer attribute to non-critical theme scripts
            add_filter('script_loader_tag', [$this, 'defer_scripts'], 10, 3);
        }

        /**
         * Disable WordPress emoji scripts and styles
         */
        public function disable_emojis()
        {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
            add_filter('tiny_mce_plugins', [$this, 'disable_emojis_tinymce']);
            add_filter('wp_resource_hints', [$this, 'disable_emojis_dns_prefetch'], 10, 2);
        }

        public function disable_emojis_tinymce($plugins)
        {
            if (is_array($plugins)) {
                return array_diff($plugins, ['wpemoji']);
            }
            return [];
        }

        public function disable_emojis_dns_prefetch($urls, $relation_type)
        {
            if ('dns-prefetch' === $relation_type) {
                $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
                foreach ($urls as $key => $url) {
                    if (strpos($url, $emoji_svg_url) !== false) {
                        unset($urls[$key]);
                    }
                }
            }
            return $urls;
        }

        /**
         * Remove bloat from wp_head
         */
        public function clean_head_tags()
        {
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        }

        /**
         * Dequeue block library CSS on frontend when using custom templates
         */
        public function dequeue_unneeded_styles()
        {
            if (!is_admin()) {
                wp_dequeue_style('wp-block-library');
                wp_dequeue_style('wp-block-library-theme');
                wp_dequeue_style('wc-blocks-style');
                wp_dequeue_style('global-styles');
                wp_dequeue_style('classic-theme-styles');
            }
        }

        /**
         * Remove X-Pingback HTTP header
         */
        public function remove_x_pingback($headers)
        {
            unset($headers['X-Pingback']);
            return $headers;
        }

        /**
         * Disable self-pingbacks
         */
        public function disable_self_pingbacks(&$links)
        {
            $home = get_option('home');
            foreach ($links as $l => $link) {
                if (0 === strpos($link, $home)) {
                    unset($links[$l]);
                }
            }
        }

        /**
         * Disable or optimize heartbeat on frontend
         */
        public function optimize_heartbeat()
        {
            if (!is_admin()) {
                wp_deregister_script('heartbeat');
            }
        }

        /**
         * Defer non-critical scripts
         */
        public function defer_scripts($tag, $handle, $src)
        {
            if (is_admin()) {
                return $tag;
            }

            $defer_handles = ['vellajous-navigation'];

            if (in_array($handle, $defer_handles, true)) {
                if (false === strpos($tag, 'defer')) {
                    return str_replace(' src', ' defer src', $tag);
                }
            }

            return $tag;
        }
    }
}
