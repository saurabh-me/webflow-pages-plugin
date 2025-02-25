<style>
  div.navbar-light {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    flex-grow: 1;
    justify-self: flex-start;
    align-self: flex-start;
    padding-left: 10px;
    margin-bottom: -1px;
  }
</style>

<div class="navbar-light">
  <slot />
</div>
