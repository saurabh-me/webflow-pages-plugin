<script>
  import { store } from "../store/wfData";
  import Submit from "./Submit.svelte";
  import Label from "./Label.svelte";
  import { writable } from "svelte/store";
  import { onMount } from "svelte";

  const hasItems = $store.staticRules.length > 0;

  let staticRule = ["", ""];

  let canAdd = false;


  const handleSubmit = async e => {
    e.preventDefault();
   
    await store.saveStaticRules($store.staticRules);
    
  };

  const addStaticRule = e => {
    e.preventDefault();
    document.activeElement.blur();
    store.addStaticRule(staticRule);
    staticRule = ["", ""];
    canAdd = false;
    store.saveStaticRules($store.staticRules);
  };

  const removeStaticRule = index => {
    store.removeStaticRule(index);
    store.saveStaticRules($store.staticRules);
  };

  const handleInput = ({ target }) => {
    let value = target.value;
    let isAdd = target.getAttribute("data-add");
    target.value = encodeURI(decodeURI(value)); // little sanitification
    value = target.value;
    const match = value.match(/^(\.?){2}(\/[a-zA-Z0-9_\-]+)+\/?$/g);
    if (match && match[0] === value || value === "/") {
      target.setCustomValidity("");
      if (isAdd) {
        canAdd = true;
      }
    } else {
      target.setCustomValidity("Invalid field.");
      if (isAdd) {
        canAdd = false;
      }
    }
  };

  const pages = $store.pages.filter( page => page !== '/404' && page !== '/401' );
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
  .no-pages-added {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
    width: 100%;
    background-color: #f7f7f7;
    border: 1px solid #f7f7f7;
  }
  .no-pages-added p {
    font-size: 14px;
    font-weight: 700;
  }
  .added-pages {
    flex: 1;
    display: inline-flex;
    flex-direction: column;
  }
  .added-pages .fields-wrapper, .added-pages .fields-headers{
    padding: 0 10px;
    background-color: #f7f8f9;
  }
</style>

<div class="wrapper">
  <div style="display: flex; flex-direction: column; justify-content: space-evenly;">
    <div class="headers">
      <h3>Static Pages</h3>
    
    </div>
    <div class="form-wrapper new-page">
    <p class="section-description">Create a new redirect</p>
      <p class="description-subtitle">Create redirects to serve a made-in-Webflow page in place of a WordPress Page</p>
      <div class="grid grid-with-actions ">
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

        <form on:submit={addStaticRule} class="grid grid-with-actions">
          <div>
            <input
              data-add="true"
              class="field-input"
              type="text"
              bind:value={staticRule[0]}
              required
              placeholder="/path"
              on:input={handleInput} />
            <small class="error-message">
              Path must start with / and be valid URLs
            </small>
          </div>
          <div>

            <select bind:value={staticRule[1]} class="field-select">
              {#each pages as page}
                <option value={page}>
                   {page.substr(1).replace('index.html', 'Home Page')}
                </option>
              {/each}
            </select>
          </div>

          <button
            type="submit"
            disabled={!canAdd}
            class="button add"
            on:click={addStaticRule}>
            +
          </button>
        </form>

      </div>

    </div>
  </div>
  <div class="added-pages">
    <p class="section-description">Rules</p>
    <form on:submit={handleSubmit} class="configuration-form">
      {#if $store.staticRules.length}
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
          {#each $store.staticRules as staticRule, index}
            <div class="input-wrapper">
              <div class="grid grid-with-actions">
                <div>
                  <input
                    class="field-input"
                    type="text"
                    bind:value={staticRule[0]}
                    readonly
                    disabled
                    on:input={handleInput} />
                </div>
                <div>
                  <select bind:value={staticRule[1]} class="field-select" disabled>
                    {#each pages as page}
                      <option value={page}>
                         {page.substr(1).replace('index.html', 'Home Page')}
                      </option>
                    {/each}
                  </select>
                </div>
                <a
                  class="button remove"
                  on:click={() => removeStaticRule(index)}>
                  X
                </a>

              </div>
            </div>
          {/each}
        </div>
      {:else}
        <div class="no-pages-added">
          <p>No rules added</p>
        </div>
      {/if}
    </form>
  </div>
</div>
