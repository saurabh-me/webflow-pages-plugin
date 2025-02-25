<?php

// Security Check
if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( "Webflow_Pages" ) ) {


	class Webflow_Pages {
		/**
		 * The unique instance of the plugin.
		 *
		 * @var Webflow_Pages
		 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return Webflow_Pages
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Adds all action and filters needed to work
		 */
		public function init() {

			// Sets locale
			$this->set_locale();


			$this->require_global_dependencies();

			if ( is_admin() ) {
				// if is on admin panel
				$this->admin_init();

			}
			$this->public_init();
		}

		/**
		 * Requires global functions
		 */
		public function require_global_dependencies() {
			require_once __DIR__ . '/misc/misc-functions.php';
		}

		/**
		 * Loads text locale
		 */
		public function set_locale()
		{
			add_action('plugins_loaded', array($this, 'load_locale'));
		}

		/**
		 * Loads plugin text domain
		 * @see 'plugins_loaded'
		 */
		public function load_locale()
		{
			load_plugin_textdomain(
				WEBFLOW_PAGES_TEXT_DOMAIN,
				false,
				WEBFLOW_PAGES_PLUGIN_DIRECTORY_PATH . '/languages/'
			);
		}

		/**
		 * Init admin side
		 */
		private function admin_init() {

			// class responsible for the admin side
			require_once __DIR__ . '/admin/class-webflow-pages-admin.php';

			// Bootstraps Admin
			Webflow_Pages_Admin::get_instance()->init();

		}

		/**
		 * Init public side
		 */
		private function public_init() {

			// class responsible for the admin side
			require_once __DIR__ . '/public/class-webflow-pages-public.php';

			// Bootstraps Public
			Webflow_Pages_Public::get_instance()->init();

			// class responsible for the Webflow APIs
			require_once __DIR__ . '/public/class-webflow-api.php';


			Webflow_API::get_instance()->init();
		}


	}

}