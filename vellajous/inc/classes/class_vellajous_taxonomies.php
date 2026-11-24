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

if (!class_exists('class_vellajous_taxonomies')) {
   class class_vellajous_taxonomies
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
      public function stx_theme_register_project_taxonomies()
      {
         // Add new taxonomy, make it hierarchical (like Project Categories)
         // *********   Project Categories  **********
         $labels = array(
            'name'               =>  _x('Project Categories', 'taxonomy general name',       vellajous_TEXT_DOMAIN),
            'singular_name'      =>  _x('Category', 'taxonomy singular name',                vellajous_TEXT_DOMAIN),
            'search_items'       =>  __('Search Categories',                                 vellajous_TEXT_DOMAIN),
            'all_items'          =>  __('All Categories',                                    vellajous_TEXT_DOMAIN),
            'view_item'          =>  __('View Categories',                                   vellajous_TEXT_DOMAIN),
            'parent_item'        =>  __('Parent Category',                                   vellajous_TEXT_DOMAIN),
            'parent_item_colon'  =>  __('Parent Category:',                                  vellajous_TEXT_DOMAIN),
            'edit_item'          =>  __('Edit Category',                                     vellajous_TEXT_DOMAIN),
            'update_item'        =>  __('Update Category',                                   vellajous_TEXT_DOMAIN),
            'add_new_item'       =>  __('Add New Category',                                  vellajous_TEXT_DOMAIN),
            'new_item_name'      =>  __('New Category Name',                                 vellajous_TEXT_DOMAIN),
            'not_found'          =>  __('No Category Found',                                 vellajous_TEXT_DOMAIN),
            'back_to_items'      =>  __('Back to Category',                                  vellajous_TEXT_DOMAIN),
            'menu_name'          =>  __('Category',                                          vellajous_TEXT_DOMAIN),
         );

         $args = array(
            'hierarchical'      =>   true,
            'labels'            =>    $labels,
            'show_ui'           =>    true,
            'show_in_menu'      =>    true,
            'show_in_nav_menus' =>    true,
            'show_admin_column' =>    true,
            'query_var'         =>    true,
            'rewrite'           =>    array('slug' => 'project-category'),
            'show_in_rest'      =>  true,  // Important for the block editor
         );

         //register new taxonomy ( Project Category )
         register_taxonomy('project-category', array('projects'), $args);


         // *********   Services Categories  **********
         $labels = array(
            'name'               =>  _x('Services Categories', 'taxonomy general name',      vellajous_TEXT_DOMAIN),
            'singular_name'      =>  _x('Category', 'taxonomy singular name',                vellajous_TEXT_DOMAIN),
            'search_items'       =>  __('Search Categories',                                 vellajous_TEXT_DOMAIN),
            'all_items'          =>  __('All Categories',                                    vellajous_TEXT_DOMAIN),
            'view_item'          =>  __('View Categories',                                   vellajous_TEXT_DOMAIN),
            'parent_item'        =>  __('Parent Category',                                   vellajous_TEXT_DOMAIN),
            'parent_item_colon'  =>  __('Parent Category:',                                  vellajous_TEXT_DOMAIN),
            'edit_item'          =>  __('Edit Category',                                     vellajous_TEXT_DOMAIN),
            'update_item'        =>  __('Update Category',                                   vellajous_TEXT_DOMAIN),
            'add_new_item'       =>  __('Add New Category',                                  vellajous_TEXT_DOMAIN),
            'new_item_name'      =>  __('New Category Name',                                 vellajous_TEXT_DOMAIN),
            'not_found'          =>  __('No Category Found',                                 vellajous_TEXT_DOMAIN),
            'back_to_items'      =>  __('Back to Category',                                  vellajous_TEXT_DOMAIN),
            'menu_name'          =>  __('Category',                                          vellajous_TEXT_DOMAIN),
         );

         $args = array(
            'hierarchical'      =>   true,
            'labels'            =>    $labels,
            'show_ui'           =>    true,
            'show_in_menu'      =>    true,
            'show_in_nav_menus' =>    true,
            'show_admin_column' =>    true,
            'query_var'         =>    true,
            'rewrite'           =>    array('slug' => 'services-category'),
            'show_in_rest'      =>  true,  // Important for the block editor
         );

         //register new taxonomy ( Project Category )
         register_taxonomy('services-category', array('services'), $args);
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
         /**
          * All Hooks
          */
         add_action('init', [$this, 'stx_theme_register_project_taxonomies']);
      }
      // --------********************--------
   }
}
