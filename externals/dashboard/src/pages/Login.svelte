<script>
  import { store } from "../store/wfData";
  import Container from "../components/Container.svelte";

  import Submit from "../components/Submit.svelte";
  import Label from "../components/Label.svelte";
  import { writable } from "svelte/store";
  import Logo from "../components/Logo.svelte";

  const loginStore = writable({
    disabled: false,
    value: "Add Webflow API Key"
  });

  let token = "";

  const handleSubmit = async e => {
    e.preventDefault();
    loginStore.set({ disabled: true, value: "Saving..." });
    await store.saveToken(token);
    loginStore.set({ disabled: false, value: "Add Webflow API Key" });
  };
</script>

<style>
  .login-wrapper {
    background-color: white;
    padding: 40px;
    border-radius: 4px;
    border: 1px solid #f7f7f7;
    width: 450px;
    text-align: center;
  }
  form {
    max-width: 500px;
    margin: auto;
  }
  p {
    text-align: center;
    margin: 0;
    margin-bottom: 30px;
    font-size: 14px;
  }
  a {
    color: black;
    text-align: center;
    outline: 0;
    box-shadow: none;
  }
  input.field-input {
    margin-bottom: 10px;
    background-color: #f7f8f9;
  }

  .warning-notice {
    background-color: #f7f8f9;
    padding: 20px;
    border-radius: 4px;
    margin-top: 20px;
  }
</style>

<Container>
  <div class="login-wrapper">
    <Logo />
    <b>Congratulations on installing the Webflow Wordpress plugin!</b>
    <p>
      You can find your site's API key by going to the <a href="https://webflow.com/dashboard?utm_source=wpplugin" target="_blank" rel="nofollow">Webflow Dashboard</a>.
    <br />Be sure to generate a <a href="https://developers.webflow.com/data/reference/site-token?utm_source=iterable&utm_medium=email&utm_campaign=developerdeprecationv1sitetoken">V2 Token</a> with at least Sites and CMS read only permissions.
    </p>
    <form on:submit={handleSubmit}>

      <input
        class="field-input"
        bind:value={token}
        type="password"
        name="api_keys"
        autocomplete="off"
        placeholder="Your Api Key..."
        required />
      <Submit value={$loginStore.value} disabled={$loginStore.disabled} />
    </form>

    <div class="warning-notice">
      This version of the plugin is not compatible with Webflow Ecommerce. Please contact <a href="https://university.webflow.com/contact" target="_blank" rel="nofollow">Webflow Support</a> for any help with the plugin.
    </div>
  </div>
</Container>
