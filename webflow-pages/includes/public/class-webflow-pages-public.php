<?php

// Security Check
if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Webflow_Pages_Public' ) ) {


	class Webflow_Pages_Public {

		/**
		 * The unique instance of the plugin.
		 *
		 * @var Webflow_Pages_Public
		 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return Webflow_Pages_Public
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Inits Actions and filters
		 */
		public function init() {

			if ( ! is_admin() ) {
				// This action must run only on the frontend, otherwise user could replace even backend paths
				add_action( 'wp', array( $this, 'filter_pages' ) );
			}

		}

		/**
		 * Gets last cached time
		 * @return int
		 */
		public function get_last_cached_time() {
			$transient = get_transient( "wf_site_cached" );
			if ( $transient ) {
				return (int) $transient;
			}

			return 0;
		}

		/**
		 * Sets last cached time transient
		 *
		 * @param int $time
		 */
		public function set_last_cached_time( $time = null ) {
			if ( ! $time ) {
				$time = time();
			}
			set_transient( "wf_site_cached", $time, 0 );
		}

		/**
		 * Filters pages if necessary
		 */
		function filter_pages() {

			$redirect_url = $this->get_redirect_url();

			if ( "" != $redirect_url ) {
				$site = wf_pages_get_url_content( $redirect_url );
				if ( ! is_wp_error( $site ) ) {
				    status_header(200); // Change status header to 200
					echo $site;
					die; // prevent the normal WordPress flow
				}
			}

		}

		/**
		 * Checks if the $wp->request matches a static or a dynamic page rule and returns the associated Redirect Url
		 *
		 * @return string
		 *
		 */
		private function get_redirect_url() {
			global $wp;
			$request = $wp->request;

			if (function_exists('is_ajax') ) {
			    if (is_ajax()) {
			        return "";
                }
            }

			if (wp_doing_ajax() || !empty( $_GET['wc-ajax']) ) {
			    return "";
            }

			if ($request == null || is_preview() || is_customize_preview()) {
				$page_id = null;
				if (isset($_GET['page_id'])) {
					$page_id = (int) $_GET['page_id'];

				} elseif (isset($_GET['p'])) {
						$page_id = (int) $_GET['p'];
				}
				if ($page_id != null) {
					$page = get_post($page_id);
					if ($page) {
						$request = $page->post_name;
						if ($request == "") { // Post has no slug (Unpublished)
							return "";
						}
					}
				}
			}

			$domain = wf_pages_get_site_domain();
			if ( "" == $domain ) {
				return "";
			}

			$static = wf_pages_get_static_page_rules();

			if ( isset( $static[ $request ] ) ) {
				$url = untrailingslashit( $domain ) . esc_url_raw( $static[ $request ] );

				if (!empty($_GET)) { // Sanitize Get
					foreach ($_GET as $key => $value) {
						$url = add_query_arg(sanitize_key($key), sanitize_title($value), $url);
					}
				}

				return esc_url($url);
			}

			$patterns = wf_pages_get_dynamic_page_rules();

			foreach ( $patterns as $pattern => $match ) {
				if ( strpos( $request, $pattern ) === 0 ) {
					$part = str_replace( $pattern, "", $request );
					if ( $part ) {
						$url = esc_url_raw( untrailingslashit( $domain ) . trailingslashit( $match ). $part );

						if (!empty($_GET)) { // Sanitize Get
							foreach ($_GET as $key => $value) {
								$url = add_query_arg(sanitize_key($key), sanitize_title($value), $url);
							}
						}

						return esc_url($url);
					}
				}
			}

			return "";
		}

	}

}