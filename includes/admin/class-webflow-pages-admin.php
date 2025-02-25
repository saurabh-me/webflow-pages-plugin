<?php

// Security Check
if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Webflow_Pages_Admin' ) ) {


	class Webflow_Pages_Admin {

		/**
	 * The unique instance of the plugin.
	 *
	 * @var Webflow_Pages_Admin
	 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return Webflow_Pages_Admin
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		// Adds actions for the Admin Side
		public function init() {

			add_action( 'admin_menu', array( $this, 'register_menu_page' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts'));

		}

		/**
		 * Register a custom menu page.
		 */
		function register_menu_page() {
			add_menu_page(
				__( 'Webflow Pages', WEBFLOW_PAGES_TEXT_DOMAIN ),
				__( 'Webflow Pages', WEBFLOW_PAGES_TEXT_DOMAIN ),
				'manage_options',
				'webflow-settings',
				"",
				'data:image/svg+xml;base64,PHN2ZyBkYXRhLXdmLWljb249IldlYmZsb3dJY29uIiB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTE1LjMzMzMgNEwxMS4yOTE1IDEySDcuNDk1MTZMOS4xODY2MyA4LjY4NDQ2SDkuMTEwNzRDNy43MTUyOCAxMC41MTg2IDUuNjMzMjMgMTEuNzI2IDIuNjY2NjMgMTJWOC43MzAzNEMyLjY2NjYzIDguNzMwMzQgNC41NjQ0NCA4LjYxNjg1IDUuNjgwMSA3LjQyOTIySDIuNjY2NjNWNC4wMDAwNkg2LjA1MzQ1VjYuODIwNUw2LjEyOTQ3IDYuODIwMThMNy41MTM0NCA0LjAwMDA2SDEwLjA3NDhWNi44MDI2MUwxMC4xNTA4IDYuODAyNDlMMTEuNTg2NyA0SDE1LjMzMzNaIiBmaWxsPSJjdXJyZW50Q29sb3IiPjwvcGF0aD48L3N2Zz4=',
				6
			);

			add_submenu_page(
				'webflow-settings',
				__( 'Welcome', WEBFLOW_PAGES_TEXT_DOMAIN ),
				__( 'Welcome', WEBFLOW_PAGES_TEXT_DOMAIN ),
				'manage_options',
				'webflow-settings',
				array($this, 'display_welcome_page')
			);

			add_submenu_page(
				'webflow-settings',
				__( 'Webflow Pages Settings', WEBFLOW_PAGES_TEXT_DOMAIN ),
				__( 'Settings', WEBFLOW_PAGES_TEXT_DOMAIN ),
				'manage_options',
				'webflow-pages-settings',
				array($this, 'display_settings_page')
			);
		}

		/**
		 *
		 * Displays main page
		 *
		 */
		function display_welcome_page() {
			include_once __DIR__ . '/views/admin-welcome-page.php';
		}

		/**
		 * Displays settings page
		 */
		function display_settings_page() {
			include_once __DIR__ . '/views/admin-settings-page.php';
		}

		/**
		 * Adds Admin scripts and styles
		 *
		 * @param $hook
		 */
		function enqueue_admin_scripts($hook) {
			if( "webflow-pages_page_webflow-pages-settings" === $hook ) {
                // enques scripts and styles of the Svelte app
                wp_enqueue_script( 'webflow-dashboard',  WEBFLOW_PAGES_PLUGIN_DIRECTORY_URL . '/externals/dashboard/public/bundle.js' , array(), WEBFLOW_PAGES_PLUGIN_VERSION , true);
                wp_enqueue_style( 'webflow-dashboard',  WEBFLOW_PAGES_PLUGIN_DIRECTORY_URL . '/externals/dashboard/public/bundle.css' , array(), WEBFLOW_PAGES_PLUGIN_VERSION );

                // sets data that is necessary for the frontend
                wp_localize_script("webflow-dashboard", "_wfAjaxData", Webflow_API::get_instance()->get_ajax_data());
			}

            if( "toplevel_page_webflow-settings" === $hook ) {

                wp_enqueue_style( 'webflow-pages-toplevel',  WEBFLOW_PAGES_PLUGIN_DIRECTORY_URL . '/includes/admin/views/assets/style.css' , array(), WEBFLOW_PAGES_PLUGIN_VERSION );

            }
		}



	}

}