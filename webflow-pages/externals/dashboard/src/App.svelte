<script>
	import Route from './components/Route.svelte';
	import LoginPage from './pages/Login.svelte';
	import ConfigurationPage from './pages/Configuration.svelte';
	import UtilsPage from './pages/Utils.svelte';
	import Notifications from './components/Notifications.svelte';
	import {store} from './store/wfData';
	import Footer from './components/Footer.svelte';

</script>
<!-- "Routes" of the dashboard -->
<Route path="/login" component={LoginPage}></Route>
<Route path="/configuration" component={ConfigurationPage}></Route>
<Route path="/settings" component={UtilsPage}></Route>

<Notifications></Notifications>
<Footer></Footer>

<style>
:global(#wpcontent) {
	padding-left: 0;
}
:global(#webflow-dashboard-root) {
	height: 100%;
	display: flex;
	min-height: calc(100vh - 32px - 65px); /* 32 px is the admin bar , 65 px is the padding bottom*/
}
</style>