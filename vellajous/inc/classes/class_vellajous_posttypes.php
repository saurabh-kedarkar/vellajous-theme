<?php
if (!defined('ABSPATH')) {
   die;
}

/**
  The Post Types in vellajous
 *
 * @since      1.0.0
 * @package    vellajous
 * @author     Yudiz Solutions Ltd.
 */

if (!class_exists('class_vellajous_posttypes')) {
   class class_vellajous_posttypes
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
       * @return vellajous_posttypes - Main instance.
       */

      public static function get_instance()
      {
         if (null == self::$instance) {
            self::$instance = new self();
         }
         return self::$instance;
      }

      /**
       * Add/ Register post types
       * --------********************--------
       */
      public function vellajous_theme_register_project_post_types()
      {
         // --------***** Projects Post Type Start ******--------
         $labels = array(
            'name'                  => _x('Projects', 'Post type general name',      vellajous_TEXT_DOMAIN),
            'singular_name'         => _x('Projects', 'Post type singular name',     vellajous_TEXT_DOMAIN),
            'menu_name'             => _x('Projects', 'Admin Menu text',             vellajous_TEXT_DOMAIN),
            'name_admin_bar'        => _x('Projects', 'Add New on Toolbar',          vellajous_TEXT_DOMAIN),
            'add_new'               => __('Add New',                                 vellajous_TEXT_DOMAIN),
            'add_new_item'          => __('Add New Projects',                        vellajous_TEXT_DOMAIN),
            'new_item'              => __('New Projects',                            vellajous_TEXT_DOMAIN),
            'edit_item'             => __('Edit Projects',                           vellajous_TEXT_DOMAIN),
            'view_item'             => __('View Projects',                           vellajous_TEXT_DOMAIN),
            'all_items'             => __('Projects',                                vellajous_TEXT_DOMAIN),
            'search_items'          => __('Search Projectss',                        vellajous_TEXT_DOMAIN),
            'parent_item_colon'     => __('Parent Projectss:',                       vellajous_TEXT_DOMAIN),
            'not_found'             => __('No Projectss found.',                     vellajous_TEXT_DOMAIN),
            'not_found_in_trash'    => __('No Projectss found in Trash.',            vellajous_TEXT_DOMAIN),
            'featured_image'        => _x('Projects Cover Image',                    vellajous_TEXT_DOMAIN),
            'set_featured_image'    => _x('Set Projects image',                      vellajous_TEXT_DOMAIN),
            'remove_featured_image' => _x('Remove Projects image',                   vellajous_TEXT_DOMAIN),
            'use_featured_image'    => _x('Use as cover image',                      vellajous_TEXT_DOMAIN),
            'archives'              => _x('Projects archives',                       vellajous_TEXT_DOMAIN),
            'attributes'            => __('Projects Attributes',                     vellajous_TEXT_DOMAIN),
            'insert_into_item'      => _x('Insert into Projects',                    vellajous_TEXT_DOMAIN),
            'uploaded_to_this_item' => _x('Uploaded to this Projects',               vellajous_TEXT_DOMAIN),
            'filter_items_list'     => _x('Filter Projects list',                    vellajous_TEXT_DOMAIN),
            'items_list_navigation' => _x('Projects list navigation',                vellajous_TEXT_DOMAIN),
            'items_list'            => _x('Projects list',                           vellajous_TEXT_DOMAIN),
         );

         $supports = array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields');

         //args
         $args = array(
            'label'              => __('Projects', vellajous_TEXT_DOMAIN),
            'labels'             => $labels,
            'description'        => 'Projects custom post type.',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'show_in_admin_bar'  => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'projects'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 40,
            'supports'           => $supports,
            'show_in_rest'       => true,
            'can_export'         => true,
            'exclude_from_search' => false,
            'menu_icon'          => 'dashicons-book',
         );

         // register Projects post type
         register_post_type('projects', $args);
         // --------***** Projects Post Type End ******--------

         // --------***** Services Post Type Start ******------

         $labels = array(
            'name'                  => _x('Services', 'Post type general name',      vellajous_TEXT_DOMAIN),
            'singular_name'         => _x('Services', 'Post type singular name',     vellajous_TEXT_DOMAIN),
            'menu_name'             => _x('Services', 'Admin Menu text',             vellajous_TEXT_DOMAIN),
            'name_admin_bar'        => _x('Services', 'Add New on Toolbar',          vellajous_TEXT_DOMAIN),
            'add_new'               => __('Add New',                                 vellajous_TEXT_DOMAIN),
            'add_new_item'          => __('Add New Services',                        vellajous_TEXT_DOMAIN),
            'new_item'              => __('New Services',                            vellajous_TEXT_DOMAIN),
            'edit_item'             => __('Edit Services',                           vellajous_TEXT_DOMAIN),
            'view_item'             => __('View Services',                           vellajous_TEXT_DOMAIN),
            'all_items'             => __('Services',                                vellajous_TEXT_DOMAIN),
            'search_items'          => __('Search Servicess',                        vellajous_TEXT_DOMAIN),
            'parent_item_colon'     => __('Parent Servicess:',                       vellajous_TEXT_DOMAIN),
            'not_found'             => __('No Servicess found.',                     vellajous_TEXT_DOMAIN),
            'not_found_in_trash'    => __('No Servicess found in Trash.',            vellajous_TEXT_DOMAIN),
            'featured_image'        => _x('Services Cover Image',                    vellajous_TEXT_DOMAIN),
            'set_featured_image'    => _x('Set Services image',                      vellajous_TEXT_DOMAIN),
            'remove_featured_image' => _x('Remove Services image',                   vellajous_TEXT_DOMAIN),
            'use_featured_image'    => _x('Use as cover image',                      vellajous_TEXT_DOMAIN),
            'archives'              => _x('Services archives',                       vellajous_TEXT_DOMAIN),
            'attributes'            => __('Services Attributes',                     vellajous_TEXT_DOMAIN),
            'insert_into_item'      => _x('Insert into Services',                    vellajous_TEXT_DOMAIN),
            'uploaded_to_this_item' => _x('Uploaded to this Services',               vellajous_TEXT_DOMAIN),
            'filter_items_list'     => _x('Filter Services list',                    vellajous_TEXT_DOMAIN),
            'items_list_navigation' => _x('Services list navigation',                vellajous_TEXT_DOMAIN),
            'items_list'            => _x('Services list',                           vellajous_TEXT_DOMAIN),
         );

         $supports = array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields');

         //args
         $args = array(
            'label'              => __('Services', vellajous_TEXT_DOMAIN),
            'labels'             => $labels,
            'description'        => 'Services custom post type.',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'show_in_admin_bar'  => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'services'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 40,
            'supports'           => $supports,
            'show_in_rest'       => true,
            'can_export'         => true,
            'exclude_from_search' => false,
            'menu_icon'          => 'dashicons-category',
         );

         // register Services post type
         register_post_type('services', $args);

         // --------***** Services Post Type End ******--------

      }
      // --------********************--------

      /**
        Add Actions/Hooks of Post Types
       *
       * @since      1.0.0
       * @package    vellajous
       * @author     Yudiz Solutions Ltd.
       * --------********************--------
       */
      public function add_actions()
      {
         /**
          * All Hooks
          */
         add_action('init', [$this, 'vellajous_theme_register_project_post_types']);
      }
      // --------********************--------
   }
}
