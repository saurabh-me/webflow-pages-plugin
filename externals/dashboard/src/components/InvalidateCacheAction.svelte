<script>
  import { store } from "../store/wfData";
  import { writable } from "svelte/store";
  import Submit from "./Submit.svelte";

  const invalidateCacheStore = writable({
    disabled: false,
    value: "Invalidate Cache"
  });

  const handleInvalidateCache = async e => {
    e.preventDefault();
    invalidateCacheStore.set({ disabled: true, value: "Wait..." });
    await store.invalidateCache();
    invalidateCacheStore.set({ disabled: false, value: "Invalidate Cache" });
  };
</script>

<h4>Invalidate Cache</h4>
<p class="description-subtitle">Deletes your WordPress site's cache, which can be helpful with debugging.</p>
<form on:submit={handleInvalidateCache}>
  <Submit
    value={$invalidateCacheStore.value}
    disabled={$invalidateCacheStore.disabled} variation="black"/>
</form>
