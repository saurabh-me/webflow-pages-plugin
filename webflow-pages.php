<?php
/**
 * Plugin Name: Webflow Pages
 * Description: Build pages visually with the power of code in Webflow, then serve them right on your WordPress site.
 * Version:     1.1.1
 * Author:      Webflow
 * Author URI:  https://webflow.com
 * Text Domain: wf
 * Domain Path: /languages
 * License:     GPL2
 */

// Security Check
if (!defined('WPINC') || !defined('ABSPATH')) {
	die;
}


// Requires core class
require __DIR__ . '/includes/class-webflow-pages.php';


// Define constants
define('WEBFLOW_PAGES_PLUGIN_VERSION', '1.1.1');
define('WEBFLOW_PAGES_PLUGIN_DIRECTORY_PATH', plugin_dir_path(__FILE__));
define('WEBFLOW_PAGES_PLUGIN_DIRECTORY_URL', plugin_dir_url(__FILE__));
define('WEBFLOW_PAGES_TEXT_DOMAIN', 'wf');


if(!function_exists('webflow_pages')) {

	/**
	 * Returns the instance of Webflow pages
	 *
	 * @return Webflow_Pages
	 */
	function webflow_pages() {
		return Webflow_Pages::get_instance();
	}


}

// Bootstrap the plugin
webflow_pages()->init();
