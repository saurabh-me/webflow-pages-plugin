<script>
  import { store } from "../store/wfData";
  import { writable } from "svelte/store";
  import Submit from "./Submit.svelte";

  const preloadCacheStore = writable({
    disabled: false,
    value: "Preload Cache"
  });

  const handlePreloadCache = async e => {
    e.preventDefault();
    preloadCacheStore.set({ disabled: true, value: "Wait..." });
    await store.preloadCache();
    preloadCacheStore.set({ disabled: false, value: "Preload Cache" });
  };
</script>

<h4>Preload static page cache</h4>
<p class="description-subtitle">Preload your static page cache to help pages load faster.</p>
<form on:submit={handlePreloadCache}>
  <Submit
    value={$preloadCacheStore.value}
    disabled={$preloadCacheStore.disabled} variation="black"/>
</form>
