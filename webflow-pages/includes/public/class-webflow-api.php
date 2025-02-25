<?php

// Security Check
if (!defined('WPINC') || !defined('ABSPATH')) {
    die;
}

// class responsible for the Webflow APIs v1
require_once __DIR__ . '/class-webflow-api-v1.php';

// class responsible for the Webflow APIs v2
require_once __DIR__ . '/class-webflow-api-v2.php';

if (!class_exists('Webflow_API')) {


    class Webflow_API
    {
        /**
         * The unique instance of the plugin.
         *
         * @var Webflow_API
         */
        private static $instance;

        private $token;

        private $domain;

        private $site;

        private $token_version;

        /**
         * Gets an instance of our plugin.
         *
         * @return Webflow_API
         */
        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function init()
        {
            // hooks ajax actions
            if (is_admin()) {
                add_action('admin_init', array($this, 'init_ajax_actions'));
                add_action( 'admin_notices', array($this, 'show_notices') );
            }
        }

        /**
         * Shows Admin Notices
         */
        public function show_notices() {

            $version = "unknown";

            if ($this->has_token()) {
                $token = $this->get_api_token();
                $version = $this->get_api_token_version($token);
            }
            
            if ($version && $version == "v1") {

                ?>
<div class="notice notice-error">
<p><?php _e( 'Webflow API V1 will sunset on <strong>January 1, 2025.</strong> Disconnect your Webflow site from Webflow Pages > Settings and reconnect it using a <a href="https://developers.webflow.com/data/reference/site-token?utm_source=iterable&utm_medium=email&utm_campaign=developerdeprecationv1sitetoken">Webflow V2 token</a>.', WEBFLOW_PAGES_TEXT_DOMAIN ); ?></p>
</div>
                <?php
            
            }
        }

        /**
         * Inits all ajax call needed from frontend
         */
        public function init_ajax_actions()
        {
            add_action("wp_ajax_save_wf_token", array($this, "save_wf_token"));
            add_action("wp_ajax_check_wf_token", array($this, "check_wf_token"));
            add_action("wp_ajax_remove_wf_token", array($this, "remove_wf_token"));
            add_action("wp_ajax_remove_wf_token_and_data", array($this, "remove_wf_token_and_data"));
            add_action("wp_ajax_get_wf_site_data", array($this, "get_wf_site_data"));
            add_action("wp_ajax_save_wf_static_rules", array($this, "save_wf_static_rules"));
            add_action("wp_ajax_save_wf_dynamic_rules", array($this, "save_wf_dynamic_rules"));
            add_action("wp_ajax_invalidate_wf_cache", array($this, "invalidate_wf_cache"));
            add_action("wp_ajax_preload_wf_cache", array($this, "preload_wf_cache"));
            add_action("wp_ajax_change_wf_cache_duration", array($this, "change_wf_cache_duration"));
        }


        /**
         * Returns site data from ajax call
         */
        public function get_wf_site_data()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax")) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            $site_data = $this->get_ajax_data();

            ob_clean();
            if (is_wp_error($site_data) || (is_array($site_data) && array_key_exists("error", $site_data))) {
                wp_send_json_error($site_data);
            } else {
                wp_send_json_success($site_data);
                wp_die();
            }
        }

        /**
         * Invalidates Cache
         */
        public function invalidate_wf_cache()
        {
            
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            wf_pages_invalidate_cache();
            wp_send_json_success();
            wp_die();
        }

        /**
         * Change WF Cache Duration from ajax call
         */
        public function change_wf_cache_duration()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            if (!isset($_POST['duration'])) {
                wp_send_json_error(new WP_Error("missing_data", "missing duration"));
                wp_die();
            }

            $duration = intval($_POST['duration']);

            wf_pages_set_cache_duration($duration);
            wp_send_json_success();
            wp_die();
        }

        /**
         * Preloads Cache of pages from ajax
         */
        public function preload_wf_cache()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            $cached_page = $this->preload_static_pages_cache();

            if (is_wp_error($cached_page)) {
                wp_send_json_error($cached_page);
                wp_die();
            } else {
                wp_send_json_success($cached_page);
                wp_die();
            }

        }

        /**
         * Preloads static pages cache
         */
        public function preload_static_pages_cache()
        {

            $domain = wf_pages_get_site_domain();
            if ("" == $domain) {
                return new WP_Error("invalid_domain", "Cannot prefetch cache with invalid domain");
            }

            $static = wf_pages_get_static_page_rules();

            $cached = 0;

            foreach ($static as $wp => $wf) {
                $url = untrailingslashit($domain) . esc_url_raw($wf);
                $res = wf_pages_get_url_content($url);
                if (!is_wp_error($res)) {
                    $cached++;
                } else {
                    return $res;
                }
            }

            return $cached;
        }

        public function get_api_manager() {
            $token = $this->get_api_token();
            $version = $this->get_api_token_version($token);

            switch($version) {
                case "v1":
                   return Webflow_APIv1::get_instance();
                 case "v2":
                    return Webflow_APIv2::get_instance();
                    break;
            }

        }

        /**
         * Returns data attached to the only site you can get with the API Token
         *
         * @return array|mixed|object|string|WP_Error
         */
        public function get_main_site_data()
        {

            $cached_site_data = get_transient("_wf_site_data");

            if ($cached_site_data) {
                return $cached_site_data;
            }

            
            $api_manager = $this->get_api_manager();

            if (!$api_manager) {
                return new WP_Error('invalid_site_data', "Invalid token, be sure to use a V2 token!");
            }
         
            $site = $api_manager->get_site();


            if (is_wp_error($site)) {
                return $site;
            }

            if (!$site) {
            	return new WP_Error('invalid_site_data', "Site data seems not valid");
            }
            //if (!$site->lastPublished) { Last published is null on site hosted only on subdomains

            // return new WP_Error('site_not_published', __("The API key you used is invalid: your site is not published", WEBFLOW_PAGES_TEXT_DOMAIN));
            //}

            $site_data = $api_manager->get_site_data($site);



            return $site_data;

        }

        /**
         * Saves static rules from Ajax Call
         */
        public function save_wf_static_rules()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            if (!isset($_POST['rules'])) {
                wp_send_json_error(new WP_Error("invalid_data", "Missing rules data"));
                wp_die();
            }

            $rules = json_decode(stripslashes(sanitize_text_field($_POST['rules'])));

            $to_save = array();

            foreach ($rules as $rule_array) {
                if (count($rule_array) != 2) {
                    continue;
                }
                $wp = esc_url_raw($rule_array[0]);
                $wf = esc_url_raw($rule_array[1]);

                $wp = ltrim($wp, "/"); // removes / at start
                $wp = rtrim($wp, "/"); // removes / at end

                $to_save[$wp] = $wf;
            }

            $this->save_static_page_rules($to_save);

            $rules_to_send = array();
            foreach ($to_save as $wp => $wf) {
                $wp = "/$wp"; // adds back the slash
                $rules_to_send[] = [$wp, $wf];
            }

            // Sends back saved rules
            wp_send_json_success($rules_to_send);

            wp_die();

        }

        /**
         * Saves static rules from Ajax Call
         */
        public function save_wf_dynamic_rules()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            if (!isset($_POST['rules'])) {
                wp_send_json_error(new WP_Error("invalid_data", "Missing rules data"));
                wp_die();
            }

            $rules = json_decode(stripslashes(sanitize_text_field($_POST['rules'])));

            $to_save = array();

            foreach ($rules as $rule_array) {
                if (count($rule_array) != 2) {
                    continue; // skip invalid rules format
                }
                $wp = esc_url_raw($rule_array[0]);
                $wf = esc_url_raw($rule_array[1]);

                $wp = ltrim($wp, "/"); // removes / at start
                $wp = rtrim($wp, "*"); // removes * at end
                $wp = rtrim($wp, "/"); // removes / at end

                $to_save[$wp . "/"] = $wf;
            }

            $this->save_dynamic_page_rules($to_save);

            $rules_to_send = array();
            foreach ($to_save as $wp => $wf) {
                $wp = "/$wp*"; // adds back the correct format
                $rules_to_send[] = [$wp, $wf];
            }

            // Sends back saved rules
            wp_send_json_success($rules_to_send);

            wp_die();

        }

        /**
         * Saves static page rules
         *
         * @param $rules
         *
         * @return bool
         */
        public function save_static_page_rules($rules)
        {
            foreach ($rules as $page_name => $webflow_url) {
                if (strpos($page_name, '/') !== false) { // We can't create nested structures as permalink
                    continue;
                }
                $this->create_page($page_name);
            }
            return update_option("_wf_static_page_rules", $rules);
        }

        public function create_page($page_name) {
                if (get_page_by_path($page_name) === NULL) {
                    $create_page_args = array(
                        'post_title'    => ucfirst($page_name),
                        'post_content'  => '',
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                        'post_type'     => 'page',
                        'post_name'     => $page_name
                    );

                    // Insert the post into the database
                    wp_insert_post( $create_page_args );
                }
        }

        /**
         * Saves dynamic page rules
         *
         * @param $rules
         *
         * @return bool
         */
        public function save_dynamic_page_rules($rules)
        {
            return update_option("_wf_dynamic_page_rules", $rules);
        }

        /**
         * Removes Wf token from the db and all the site associated data
         *
         *
         */
        public function remove_wf_token()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }
            $this->remove_api_token(false);
            wp_send_json_success();
            wp_die();
        }

        /**
         * Removes Wf token from the db and all the site associated data
         *
         *
         */
        public function remove_wf_token_and_data()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }
            $this->remove_api_token(true);
            wp_send_json_success();
            wp_die();
        }


        /**
         * Saves wf api token
         */
        public function save_wf_token()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            if (!isset($_POST['token'])) {
                wp_send_json_error(new WP_Error("missing_data", "missing token"));
                wp_die();
            }

            $token = sanitize_text_field($_POST['token']);

            $this->set_api_token($token);
            wp_send_json_success();
            wp_die();
        }

 /**
         * Check wf api token version
         */
        public function check_wf_token()
        {
            if (empty($_POST) || !wp_verify_nonce($_POST['security'], "_wf_ajax") || !current_user_can('administrator')) {
                wp_send_json_error(new WP_Error("wp_nonce_verify", "failed security check"));
                wp_die();
            }

            if (!isset($_POST['token'])) {
                wp_send_json_error(new WP_Error("missing_data", "missing token"));
                wp_die();
            }

            $token = sanitize_text_field($_POST['token']);

            $this->token_version = "";
            delete_option('_wf_api_token_version');
            $version = $this->get_api_token_version($token);
            wp_send_json_success(["version" => $version]);
            wp_die();
        }

        /**
         * Returns data needed for the localize script function
         * @return array
         */
        public function get_ajax_data()
        {

            $site_data = $this->get_main_site_data();

            if (is_wp_error($site_data)) {

                $data = array(
                    "hasToken" => $this->has_token() ? "true" : "false",
                    "nonce" => wp_create_nonce("_wf_ajax"),
                );
                if ($this->has_token()) {
                    $this->remove_api_token(false);
                    // Token with invalid site data
                    $error = $site_data->get_error_message();
                    $data["error"] = $error ? $error : __("Token removed, due to invalid site data", WEBFLOW_PAGES_TEXT_DOMAIN);
                }

                return $data;
            } else {

                $static_rules = wf_pages_get_static_page_rules();

                // frontend requires a / instead of "" for home page format of the rules
                $static_rules_to_send = array();

                foreach ($static_rules as $wp => $wf) {
                    if ("" == $wp || "/" != $wp[0]) { // recovers db errors
                        $wp = "/$wp";
                    }
                    $static_rules_to_send[] = [$wp, $wf];
                }

                $dynamic_rules = wf_pages_get_dynamic_page_rules();

                // frontend requires a / instead of "" for home page format of the rules
                $dynamic_rules_to_send = array();

                foreach ($dynamic_rules as $wp => $wf) {
                    if ("" == $wp || "/" != $wp[0]) { // recovers db errors
                        $wp = "/$wp*";
                    }
                    $dynamic_rules_to_send[] = [$wp, $wf];
                }

                return array(
                    "hasToken" => $this->has_token() ? "true" : "false",
                    "nonce" => wp_create_nonce("_wf_ajax"),
                    "pages" => $site_data['pages'],
                    "collections" => $site_data['collections'],
                    "site" => $site_data['site'],
                    "staticRules" => $static_rules_to_send,
                    "dynamicRules" => $dynamic_rules_to_send,
                    "cacheDuration" => wf_pages_get_cache_duration(),
                );
            }

        }

        /**
         * Returns true if user has a token saved on db
         *
         * @return bool
         */
        public function has_token()
        {
            return $this->get_api_token() != "";
        }

        public function get_api_token()
        {
            if ($this->token) {
                return $this->token;
            } else {
                $token = get_option('_wf_api_token');

                if ($token) {
                    $this->token = $token;

                    return $token;
                } else {
                    return "";
                }
            }
        }

        public function get_api_token_version($token) {
            if ($this->token_version) {
                return $this->token_version;
            }


            if (!$token) {
                return "unknown";
            }
            $this->token = $token;

            $version = get_option('_wf_api_token_version');
            if ($version) {
                $this->token_version = $version;

                return $version;
            }

            $infov2 = Webflow_APIv2::get_instance()->get_authorization_info();

            if (is_wp_error($infov2)) {

                $infov1 = Webflow_APIv1::get_instance()->get_authorization_info();

                if (is_wp_error($infov1)) {
                    $version = "unknown";
                    return $version;
                } else {
                    $version = "v1";
                }
            } else {

                $version = "v2";

            }

            $this->token_version = $version;

            update_option('_wf_api_token_version', $version);

            return $version;
            
        }

        

           /**
         * Saves Api Token on db
         *
         * @param $token
         */
        public function set_api_token($token)
        {
            delete_option("_wf_api_token_version");
            $version = $this->get_api_token_version($token);
           
            if ($version !== "v2") {
                delete_option("_wf_api_token");
            } else {
                update_option('_wf_api_token', $token);
            }
           
        }

       /**
         * Removes Api Token
         */
        public function remove_api_token($delete_rules)
        {
            delete_option('_wf_api_token');
            delete_option('_wf_api_token_version');
            $this->token = null;
            $this->token_version = null;
            wf_pages_invalidate_cache();
            if ($delete_rules) {
                wf_pages_delete_rules();
            }
            
        }
    }

}