<script>
  import { store } from "../store/wfData";
  import { writable } from "svelte/store";
  import Submit from "./Submit.svelte";

  const localStore = writable({
    disabled: false,
    value: "Save"
  });

  let val = $store.cacheDuration;

  const options = [
    {
      value: "0",
      name: "As long as possible"
    },
    {
      value: "60",
      name: "1 minute"
    },
    {
      value: 60 * 5 + "",
      name: "5 minutes"
    },
    {
      value: 60 * 15 + "",
      name: "15 minutes"
    },
    {
      value: 60 * 60 + "",
      name: "1 hour"
    },
    {
      value: 60 * 60 * 24 + "",
      name: "1 day"
    }
  ];

  const handleSubmit = async e => {
    e.preventDefault();
    localStore.set({ disabled: true, value: "Wait..." });
    await store.changeCache(val);
    localStore.set({ disabled: false, value: "Save" });
  };
</script>
<style>
form {
display: grid;
    grid-gap: 8px;
    justify-content: center;
    align-items: center;
    grid-template-columns: 1fr 1fr;
}
select {
  background-color: #f7f8f9;
}

</style>
<h4>Cache Duration</h4>
<p class="description-subtitle">Define how long your site caches your Webflow pages. Cached pages load faster.</p>
<form on:submit={handleSubmit} >
  <select class="field-select" bind:value={val}>
    {#each options as option}
      <option value={option.value}> {option.name} </option>
    {/each}
  </select>
  <Submit
    value={$localStore.value}
    disabled={$localStore.disabled}
    variation="small"
   />
</form>
