<?php

// Security Check
if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
	die;
}

delete_transient("_wf_site_data");

?>
<!-- root of the Svelte App -->
<div id="webflow-dashboard-root"></div>

