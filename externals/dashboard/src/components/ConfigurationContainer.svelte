<script>
  import Logo from "./Logo.svelte";
  import Navigation from "./Navigation.svelte";
</script>

<style>
  div.configuration-container {
    width: 100%;
    margin-right: auto;
    margin-left: auto;
    position: relative;
    
    height: calc(100vh - 32px);
    display: flex;
    height: calc(100vh - 32px);
    flex-direction: column;
    justify-content: space-evenly;
  }
  .wrapper {
    margin: 0 auto;
    height: calc(100vh - 80px);
    width: 80%;
  }
  .main-content {
    background-color: white;
    border: 1px solid #f7f7f7;
    border-radius: 4px;
    display: grid;
    padding: 15px;
    grid-template-columns: 1fr 1fr;
    grid-gap: 16px;
    height: 80%;
    align-items: center;
    justify-content: space-between;
  }
  @media screen and (max-width: 782px) {
    .main-content {
      grid-template-columns: 1fr;
      height: auto;
    }
  }
</style>

<div class="configuration-container">
  <Logo />
  <div class="wrapper">
    <Navigation />
    <div class="main-content">
      <slot />
    </div>
  </div>
</div>
