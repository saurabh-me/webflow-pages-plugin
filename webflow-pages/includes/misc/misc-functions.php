<?php

/**
 * Misc functions
 */


if (!function_exists('wf_pages_delete_prefixed_transients')) {
	/**
	 * Deletes all transients that starts with the prefix
	 *
	 * @param $prefix
	 */
	function wf_pages_delete_prefixed_transients($prefix) {
		global $wpdb;

		$prefix = $wpdb->esc_like( '_transient_' . $prefix . '_' );
		$sql = "DELETE FROM $wpdb->options WHERE `option_name` LIKE '%s'";
		$wpdb->query( $wpdb->prepare( $sql, $prefix . '%' ));

	}
}


if (!function_exists('wf_pages_invalidate_cache')) {
	/**
	 * Removes all caching transients and deletes wf site data
	 */
	function wf_pages_invalidate_cache() {

		wf_pages_delete_prefixed_transients( "wf_site_page" );

		delete_transient( "_wf_site_data" );

	}

}


if (!function_exists('wf_pages_delete_rules')) {

	/**
	 * Removes static and dynamic page rules
	 */
	function wf_pages_delete_rules() {
		delete_option("_wf_static_page_rules");
		delete_option("_wf_dynamic_page_rules");
	}
}

if (!function_exists('wf_pages_get_static_page_rules')) {

	/**
	 * Gets static page rules returns empty array as default
	 * @return mixed
	 */
	function wf_pages_get_static_page_rules() {
		return get_option("_wf_static_page_rules", array());
	}
}

if (!function_exists('wf_pages_get_dynamic_page_rules')) {

	/**
	 * Gets cms page rules returns empty array as default
	 * @return mixed
	 */
	function wf_pages_get_dynamic_page_rules() {
		return get_option("_wf_dynamic_page_rules", array());
	}
}


if (!function_exists('wf_pages_get_site_domain')) {

	/**
	 * Gets wf site domain associated with current wf API Token, returns an empty string on error
	 *
	 * @return string
	 */
	function wf_pages_get_site_domain() {
		$site_data = Webflow_API::get_instance()->get_main_site_data();
		if (is_wp_error($site_data) || !$site_data) {
			return "";
		} else {
			return $site_data['site']->domain;
		}

	}
}


if (!function_exists('wf_pages_get_url_content')) {

	/**
	 * Gets url page content using curl or file get contents as fallback
	 *
	 * @param $url
	 *
	 * @return bool|mixed|string|WP_Error
	 */
	function wf_pages_get_url_content( $url ) {

        $url = esc_url_raw( $url );

        $data = get_transient( "wf_site_page_$url" );

        if ( $data ) {
            // cache hit
            return $data;
        }

        $response = wp_remote_get( $url );

        $http_code = wp_remote_retrieve_response_code( $response );

        if (200 == $http_code) {
            $body = wp_remote_retrieve_body( $response );
            set_transient( "wf_site_page_$url", $body, wf_pages_get_cache_duration() );

            // last time a site got cached
            set_transient( "wf_site_cached", time(), 0 );

            return $body;
        } else {
            return new WP_Error( "failed_get_content", "Get content to $url failed" );
        }

    }
}

if (!function_exists('wf_pages_get_cache_duration')) {

	/**
	 * Returns Cache duration defaults on 60 seconds
	 * @return int
	 */
	function wf_pages_get_cache_duration() {
		return get_option("wf_pages_cache_duration", 60);
	}

}

if (!function_exists('wf_pages_set_cache_duration')) {

	/**
	 *
	 * Saved cache duration in seconds
	 *
	 * @param $duration
	 *
	 * @return bool
	 */
	function wf_pages_set_cache_duration( $duration ) {
		$duration = absint( $duration );
		return update_option("wf_pages_cache_duration", $duration );
	}

}