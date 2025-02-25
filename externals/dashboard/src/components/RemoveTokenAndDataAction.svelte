<script>
  import { store } from "../store/wfData";
  import { writable } from "svelte/store";
  import Submit from "./Submit.svelte";
  import { fade } from 'svelte/transition';

  const removeTokenStore = writable({
    disabled: false,
    value: "Remove site and data"
  });

  const modalStore = writable({
    open: false
  })

  const handleRemoveToken = async e => {
    e.preventDefault();
    const res = await modalResponse();
    if (!res) {
      return;
    }
    removeTokenStore.set({ disabled: true, value: "Wait..." });
    await store.removeTokenAndData();
    removeTokenStore.set({ disabled: false, value: "Remove site and data" });
  };



  const closeModal = e => {
    e.preventDefault();
    document.body.dispatchEvent(new CustomEvent('webflow-modal-remove-token-data', {detail: false}));
  }

  const acceptModal = e => {
    e.preventDefault();
    document.body.dispatchEvent(new CustomEvent('webflow-modal-remove-token-data', {detail: true}));
  }

  const modalResponse = () => {
    return new Promise( res => {
      modalStore.set({open: true});
      document.body.addEventListener('webflow-modal-remove-token-data', e => {
        modalStore.set({open: false})
        res(e.detail);
      }, {once: true});
    })
  }

</script>

<style>
  .modal-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 99999999;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .modal {
    background-color: white;
    height: 30vh;
    width: 30vw;
    display: grid;
    grid-template-rows: auto 1fr;
  }
  .modal-header {
    display: flex;
    padding: 20px 15px;
    justify-content: space-between;
    font-weight: 700;
    font-size: 16px;
    border-bottom: 1px solid #f7f7f7;
  }
  .modal-header a {
    color: black;
    text-decoration: none;
  }
  .modal-content {
    background-color: #f5f5f5;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  .modal-button {
    display: inline-block;
    padding: 9px 15px;
    color: white;
    border: 0;
    line-height: inherit;
    text-decoration: none;
    cursor: pointer;
    border-radius: 4px;
  }
  .modal-button.red {
    width: auto;
    height: auto;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #FF6382;
    font-weight: 500;
  }

  .modal-button.red:hover {
    background-color: #FF6382;
  }

  .modal-button.grey{
    width: auto;
    height: auto;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #d2d4d5;
    font-weight: 500;
  }

  .modal-button.grey:hover {
    background-color: #d2d4d5;
  }
</style>

<h4>Remove Website and Data</h4>
<p class="description-subtitle">Disconnect your Webflow project from your WordPress site and remove saved pages settings.</p>
<form on:submit={handleRemoveToken}>
  <Submit
    value={$removeTokenStore.value}
    disabled={$removeTokenStore.disabled}
    variation="red" />
</form>

{#if $modalStore.open}
<div class="modal-wrapper">
  <div class="modal" transition:fade>
    <div class="modal-header">
      <div>Do you want to remove all the Webflow Pages?</div>
      <a on:click={closeModal}>X</a>
    </div>
    <div class="modal-content">
    <p>Are you sure you want to remove all your Webflow Settings?</p>
        <div class="modal-actions">
          <button class="modal-button grey" on:click={closeModal}>Cancel</button>
          <button class="modal-button red" on:click={acceptModal}>Confirm</button>
        </div>
    </div>
  </div>
</div>
{/if}