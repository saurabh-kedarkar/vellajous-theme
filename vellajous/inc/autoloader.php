<?php
// exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
  Autoloader file for theme.
 * 
 * @since      1.0.0
 * @package    vellajous
 * @author     Yudiz Solutions Ltd.
 */

// Autoloader function for classes without namespaces
function vellajous_classes_autoloader($class_name)
{

   // Base directory for class files
   $base_dir = __DIR__ . '/classes/';

   $class_name  = trim($class_name, '\\');

   if (empty($class_name)) {
      // Not our namespace, bail out.
      return;
   }

   // Convert the class name into a file path
   $file = $base_dir . strtolower($class_name) . '.php';

   $real_base_dir = realpath($base_dir);
   $real_file = realpath($file);

   // Check if the file is within the base directory
   if ($real_file && strpos($real_file, $real_base_dir) === 0) {
      // If the file exists within the base directory, require it
      if (file_exists($real_file)) {
         require_once($real_file);
      }
   }
}
/**
  called function in autoloader
 *
 * @since      1.0.0
 * @package    vellajous
 * @author     Yudiz Solutions Ltd.
 */
// Register the autoloader
spl_autoload_register('vellajous_classes_autoloader');
