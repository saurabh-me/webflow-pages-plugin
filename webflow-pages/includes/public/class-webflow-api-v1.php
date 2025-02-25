<?php

// Security Check
if (!defined('WPINC') || !defined('ABSPATH')) {
    die;
}

if (!class_exists('Webflow_APIv1')) {


    class Webflow_APIv1
    {
        /**
         * The unique instance of the plugin.
         *
         * @var Webflow_APIv1
         */
        private static $instance;

        private $domain;

        private $site;

        /**
         * Gets an instance of our plugin.
         *
         * @return Webflow_APIv1
         */
        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
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

            $site_data = $this->get_main_site_data();

            if (is_wp_error($site_data)) {
                wp_send_json_error($site_data);
            } else {
                wp_send_json_success($site_data);
                wp_die();
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

            $site = $this->get_site();

            if (is_wp_error($site)) {
                return $site;
            }

            if (!$site) {
            	return new WP_Error('invalid_site_data', "Site data seems not valid");
            }
            //if (!$site->lastPublished) { Last published is null on site hosted only on subdomains

            // return new WP_Error('site_not_published', __("The API key you used is invalid: your site is not published", WEBFLOW_PAGES_TEXT_DOMAIN));
            //}

            $site_data = $this->get_site_data($site);

            return $site_data;

        }


        /**
         * Removes Wf token from the db and all the site associated data
         *
         *
         */
        public function remove_wf_token()
        {
            return Webflow_API::get_instance()->remove_wf_token(false);
        }

        /**
         * Saves wf api token
         */
        public function save_wf_token()
        {
            return Webflow_API::get_instance()->save_wf_token();
        }

        public function get_static_pages($site)
        {

            $domain_response = $this->get_site_domain($site);

            if (!is_wp_error($domain_response)) {
                $res = wp_remote_get("https://" . trailingslashit($domain_response) . "static-manifest.json");
                return $this->handle_remote_response($res, new WP_Error('static_pages', __("The API key failed. Try publishing your Webflow site first", WEBFLOW_PAGES_TEXT_DOMAIN)));
            } else {
                return $domain_response;
            }
        }

        /**
         * Gets Site data associated: [site, pages, collections]
         *
         * @param $site
         *
         * @return array|mixed|object|string|WP_Error
         */
        public function get_site_data($site)
        {

            if (is_wp_error($site) || !$site) {
                delete_transient("_wf_site_data");
                return $site;
            }
            $pages = $this->get_static_pages($site);

            if (is_wp_error($pages)) {
                delete_transient("_wf_site_data");

                return $pages;
            }
            $collections = $this->list_collections($site->_id);
            if (is_wp_error($collections)) {
                delete_transient("_wf_site_data");

                return $collections;
            }

            $site_domain = $this->get_site_domain($site);
            if (is_wp_error($site_domain)) {
                delete_transient("_wf_site_data");

                return $site_domain;
            }
            $site->domain = "https://$site_domain";

            $site_data = array(
                "site" => $site,
                "pages" => $pages,
                "collections" => $collections
            );

            set_transient("_wf_site_data", $site_data, 0);

            return $site_data;
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

        /**
         * Saves Api Token on db
         *
         * @param $token
         */
        private function set_api_token($token)
        {
            return Webflow_API::get_instance()->set_api_token($oken);
        }

        /**
         * Removes Api Token
         */
        private function remove_api_token()
        {
            return Webflow_API::get_instance()->remove_api_token(false);
        }

        /**
         * Gets Webflow Api Token from db
         *
         * @return string
         */
        private function get_api_token()
        {
           return Webflow_API::get_instance()->get_api_token();
        }

        /**
         * Returns authorization headers needed for api call
         * @return array
         */
        private function get_webflow_headers()
        {

            $token = $this->get_api_token();

            return array(
                "Authorization" => "Bearer $token",
                "accept-version" => "1.0.0",
                "Content-Type" => "application/json; charset=utf-8",
                "user-agent" => "WordPress-Webflow Plugin " . WEBFLOW_PAGES_PLUGIN_VERSION //user-agent must be in lower case to be accepted by the WordPress core apis
            );

        }

        /**
         * Returns the first domain associated with the site or WP_Error
         *
         * @param $site
         *
         * @return string|WP_Error
         */
        public function get_site_domain($site)
        {

            if ($this->domain) {
                return $this->domain;
            }

            $domains_response = $this->get_site_domains($site->_id);
            if (!is_wp_error($domains_response)) {
                if (count($domains_response) > 0) {
                    $this->domain = $domains_response[0]->name;

                    return $this->domain;
                } else {
                    $this->domain = $site->shortName . ".webflow.io";

                    return $this->domain;
                }
            } else {
                return $domains_response;
            }

        }

        public function get_authorization_info() {
            $response = wp_remote_get("https://api.webflow.com/info", array(
                "headers" => $this->get_webflow_headers(),
            ));

            return $this->handle_remote_response($response, new WP_Error('info', __("The API key you used is invalid: failed to get token info", WEBFLOW_PAGES_TEXT_DOMAIN)));
        }

        /**
         * With API token you can get only the site for that token
         *
         * @return array|mixed|WP_Error
         */
        public function get_site()
        {

            if ($this->site) {
                return $this->site;
            }

            $list_response = $this->list_sites(); // With single token api you get only 1 site

            if (!is_wp_error($list_response) && !empty($list_response)) {
                $this->site = $list_response[0];

                return $this->site;
            } else {
                return $list_response;
            }
        }


        /**
         * Lists Webflow Sites
         *
         * returns an array of Object made as {_id: string, createdOn: Date, name: string, shortName: string, lastPublished: string, previewUrl: string}
         *
         * @return array|WP_Error
         */
        public function list_sites()
        {

            $response = wp_remote_get("https://api.webflow.com/sites", array(
                "headers" => $this->get_webflow_headers(),
            ));

            return $this->handle_remote_response($response, new WP_Error('list_sites', __("The API key you used is invalid: failed to list sites", WEBFLOW_PAGES_TEXT_DOMAIN)));
        }

        /**
         *
         * Gets site domains
         *
         * @param $site_id
         *
         * returns an array of Object {_id: string, name: string}
         *
         * @return array|WP_Error
         */
        public function get_site_domains($site_id)
        {

            $response = wp_remote_get(esc_url_raw("https://api.webflow.com/sites/$site_id/domains"), array(
                "headers" => $this->get_webflow_headers(),
            ));

            return $this->handle_remote_response($response, new WP_Error('site_domains', __("The API key you used is invalid: failed to list your site domains", WEBFLOW_PAGES_TEXT_DOMAIN)));
        }

        /**
         *
         * Lists the collections of the CMS
         *
         * @param $site_id
         *
         * returns an array of objects {_id: string, lastUpdated: date, createdOn: date, name: string, slug: string, singularName: string}
         *
         * @return array|mixed|object|WP_Error
         */
        public function list_collections($site_id)
        {

            $response = wp_remote_get(esc_url_raw("https://api.webflow.com/sites/$site_id/collections"), array(
                "headers" => $this->get_webflow_headers(),
            ));

            return $this->handle_remote_response($response, new WP_Error('list_collections', __("The API key you used is invalid: failed to list your CMS Collections", WEBFLOW_PAGES_TEXT_DOMAIN)));
        }

        /**
         * Checks the response and responds accordingly
         *
         * @param $response WP_Error|array
         * @param $wp_error WP_Error|null
         *
         * @return array|mixed|object|WP_Error
         */
        private function handle_remote_response($response, $wp_error = null)
        {
            if (is_wp_error($response) || !isset($response['response']) || !isset($response['response']['code']) || !is_array($response)) {
                return $wp_error;
            }
            $response_code = $response['response']['code'];

            if (200 == $response_code) {

                return json_decode($response['body']);

            } else {
                try {
                    $error = json_decode($response['body']);
                    if (!$error && $ops = json_last_error_msg()) {
                        return $wp_error;
                    }
                    if (401 == $error->code) {
                        $this->remove_api_token();
                    }
                    return new WP_Error($error->code, $error->msg);
                } catch (Exception $e) {
                    // $message = $e->getMessage();
                    return $wp_error;
                }
            }
        }

    }

}