<script>
  import { store } from "../store/wfData";
  import Submit from "./Submit.svelte";
  import Label from "./Label.svelte";
  import { writable } from "svelte/store";

  const hasItems = $store.dynamicRules.length > 0;

  const firstCollection = $store.collections[0];

  let dynamicRule = ["", ""];
  if ($store.collections.length) {
    dynamicRule = [``, `/${$store.collections[0].slug}/`];
  }

  let canAdd = false;

  const handleSubmit = async e => {
    e.preventDefault();

    await store.saveDynamicRules($store.dynamicRules);
  };

  const addDynamicRule = e => {
    canAdd = false;
    e.preventDefault();
    document.activeElement.blur();
    if (!dynamicRule || !dynamicRule[0]) {
      canAdd = false;
      return;
    }
    store.addDynamicRule(dynamicRule);
    dynamicRule = [``, ``];
    store.saveDynamicRules($store.dynamicRules);
  };

  const removeDynamicRule = index => {
    store.removeDynamicRule(index);
    store.saveDynamicRules($store.dynamicRules);
  };

  const handleInput = e => {
    let value = e.target.value;
    e.target.value = encodeURI(decodeURI(value)); // little sanitification
    let isAdd = e.target.getAttribute("data-add");
    value = e.target.value;
    const match = value.match(/\/.*?\/\*/g);
    if (match && match[0] === value) {
      e.target.setCustomValidity("");
      if (isAdd) {
        canAdd = true;
      }
    } else {
      e.target.setCustomValidity("Invalid field.");
      if (isAdd) {
        canAdd = false;
      }
    }
  };
</script>

<style>
  
  .headers h3 {
    margin: 0;
    height: 10%;
     margin-bottom: 6px;
  }
  .form-wrapper {
    display: flex;
    flex-direction: column;
    height: auto;
    padding: 0px 10px;
    border-radius: 4px;
    margin-bottom: 10px;
  }

  .form-wrapper.new-page {
    background-color: #f7f8f9;
  }

  form.configuration-form {
    border-radius: 4px;
    height: calc(100% - 32px);
    width: 100%;
    display: inline-flex;

    flex-direction: column;
  }
  
  .section-description {
    font-size: 12px;
    font-weight: bold;
  }
  .no-collections {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
    width: 100%;
    background-color: #f7f7f7;
    border: 1px solid #f7f7f7;
    min-height: 90px;
  }
  .no-collections p {
    font-size: 14px;
    font-weight: 700;
  }
  .added-pages {
    flex: 1;
    display: inline-flex;
    flex-direction: column;
  }
  .added-pages .fields-wrapper,
  .added-pages .fields-headers {
    padding: 0 10px;
    background-color: #f7f8f9;
  }

  :global(.fields-wrapper) {
    flex: 1;
    overflow: auto;
    max-height: calc(50px * 5);
  }
e
:global(.fields-wrapper::-webkit-scrollbar){
	width: 3px;
	background-color: #F5F5F5;
}

:global(.fields-wrapper::-webkit-scrollbar-thumb){
	background-color: #a1a1a1;
}
</style>

<div class="wrapper">
  <div
    style="display: flex; flex-direction: column; justify-content: space-evenly;">
    <div class="headers">
      <h3>Collection Pages</h3>
    </div>
    <div class="form-wrapper new-page">
      <p class="section-description">Create a new Rule</p>
      {#if $store.collections.length > 0}
        <div class="grid grid-with-actions">
          <div>
            <Label>WordPress Path</Label>
          </div>
          <div>
            <Label>Webflow Page</Label>
          </div>
          <div style="visibility:hidden">
            <button disabled class="button add">+</button>
          </div>
        </div>
        <div class="input-wrapper">
          <form on:submit={addDynamicRule} class="grid grid-with-actions">

            <div>

              <input
                data-add="true"
                class="field-input"
                type="text"
                bind:value={dynamicRule[0]}
                required
                placeholder="/path/*"
                on:input={handleInput} />
              <small class="error-message">
                Path must start with / and be valid URLs and end with /*
              </small>
            </div>
            <div>

              <select bind:value={dynamicRule[1]} class="field-select">
                {#each $store.collections as collection}
                  <option value={'/' + collection.slug + '/'}>
                    /{collection.slug}/*
                  </option>
                {/each}
              </select>
            </div>

            <button
              type="submit"
              disabled={!canAdd}
              class="button add"
              on:click={addDynamicRule}>
              +
            </button>
          </form>
        </div>
      {:else}
        <div class="no-collections">
          <p>No collections</p>
        </div>
      {/if}
    </div>
  </div>
  <div class="added-pages">
    <p class="section-description">Rules</p>
    <form on:submit={handleSubmit} class="configuration-form">
      {#if $store.dynamicRules.length}
        <div class="grid grid-with-actions fields-headers">
          <div>
            <Label>WordPress Path</Label>
          </div>
          <div>
            <Label>Webflow Page</Label>
          </div>
          <div style="visibility:hidden">
            <button disabled class="button add">+</button>
          </div>
        </div>
        <div class="fields-wrapper">
          {#each $store.dynamicRules as dynamicRule, index}
            <div class="input-wrapper">
              <div class="grid grid-with-actions">
                <div>
                  <input
                    class="field-input"
                    type="text"
                    bind:value={dynamicRule[0]}
                    required
                    disabled
                    on:input={handleInput}
                    readonly />

                </div>
                <div>
                  <select
                    bind:value={dynamicRule[1]}
                    class="field-select"
                    disabled>
                    {#each $store.collections as collection}
                      <option value={'/' + collection.slug + '/'}>
                        /{collection.slug}/*
                      </option>
                    {/each}
                  </select>
                </div>

                <a
                  class="button remove"
                  on:click={() => removeDynamicRule(index)}>
                  X
                </a>

              </div>
            </div>
          {/each}
        </div>
      {:else}
        <div class="no-collections">
          <p>No rules added</p>
        </div>
      {/if}
    </form>
  </div>

</div>
