import App from './App.svelte';


	const data = window._wfAjaxData;
	new App({
		target: document.getElementById("webflow-dashboard-root"), // inits app
		data
	});
