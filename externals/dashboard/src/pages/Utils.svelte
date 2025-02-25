<script>
  import Container from "../components/ConfigurationContainer.svelte";
  import Navigation from "../components/Navigation.svelte";
  import RemoveTokenAction from "../components/RemoveTokenAction.svelte";
  import InvalidateCacheAction from "../components/InvalidateCacheAction.svelte";
  import PreloadCacheAction from "../components/PreloadCacheAction.svelte";
  import ChangeCacheAction from "../components/ChangeCacheAction.svelte";

  import Site from "../components/Site.svelte";
  import RemoveTokenAndDataAction from "../components/RemoveTokenAndDataAction.svelte";
</script>

<style>
  .actions-container {
    display: flex;
    height: 100%;
    flex-direction: column;
    overflow: auto;
    overflow: auto;
    justify-content: space-evenly;
  }
  hr {
    border-color: #f7f7f7;
    margin: 10px;
  }
  .site-container {
    display: flex;
    height: 100%;
    overflow: auto;
    justify-content: center;
    align-items: center;
  }
  .actions-container p {
    margin: 0;
    line-height: 1;
  }
  :global(.actions-container h4) {
    margin: 0;
  }
</style>

<Container>
  <div class="actions-container">
    <p>These advanced settings control how your site loads your Webflow pages. <br />For the most part, you shouldn't need to mess with them.</p>
    <ChangeCacheAction />
    <hr />
    <InvalidateCacheAction />
    <hr />
    <PreloadCacheAction />
    <hr />
    <RemoveTokenAction />
    <hr />
    <RemoveTokenAndDataAction />
  </div>
  <div class="site-container">
    <Site />
  </div>
</Container>
