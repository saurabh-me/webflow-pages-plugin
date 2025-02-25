<script>
  import { store } from "../store/wfData";
  import Container from "../components/ConfigurationContainer.svelte";
  import StaticRules from "../components/StaticRules.svelte";
  import Link from "../components/Link.svelte";
  import CollectionRules from "../components/CollectionRules.svelte";

  //store.removeToken();
</script>

<style>
  div.spacer {
    height: 100px;
  }
  :global(form:invalid input[type="submit"]) {
    opacity: 0.7;
    cursor: not-allowed;
  }

  :global(input.field-input) {
    height: 50px;
    padding: 11px 16px;
    background-color: white;
    display: block;
    width: 100%;
    height: 38px;
    padding: 8px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333333;
    border: 1px solid transparent;
    box-shadow: none;
  }
  :global(input.field-input:focus) {
    border-color: #3898ec;
    outline: 0;
    box-shadow: none;
  }
  :global(input.field-input:read-only:focus) {
    outline: 0;
    box-shadow: none;
  }

  :global(select.field-select[disabled]) {
    -webkit-appearance: none;
    box-shadow: none;
    height: 50px;
    padding-right: 16px;
    padding-left: 16px;
    border: 1px none #000!important;
    border-radius: 0px;

    background-color: white!important;
    display: block;
    width: 100%;
    height: 38px;
    padding: 8px 12px;

    font-size: 14px;
    line-height: 1.42857143;
    color: #000;
  }

  :global(#wpbody-content) {
    float: none;
    padding-bottom: 0;
  }

  :global(#wpfooter) {
    display: none;
  }
  :global(.main-content .wrapper) {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  }

  :global(small.error-message) {
    display: none;
    position: absolute;
    bottom: 2px;
  }
  :global(.wp-core-ui .button-disabled, .wp-core-ui
      .button-secondary.disabled, .wp-core-ui
      .button-secondary:disabled, .wp-core-ui
      .button-secondary[disabled], .wp-core-ui .button.disabled, .wp-core-ui
      .button:disabled, .wp-core-ui .button[disabled]) {
    background-color: #c5c5c5 !important;
    color: white !important;
    box-shadow: none !important;
    border: none !important;
    text-shadow: none !important;
    font-size: 14px;
    width: 30px;
    height: 30px;
    vertical-align: middle;
    padding: 0;
    margin: 0;
  }
  :global(input.field-input:invalid:focus) {
    border: 1px solid red;
  }
  :global(input.field-input:focus:invalid ~ small.error-message) {
    display: block;
  }

  :global(div.input-wrapper) {
    position: relative;
    padding-bottom: 22px;
  }

  :global(select.field-select) {
    box-shadow: none;
    height: 50px;
    padding-right: 16px;
    padding-left: 16px;
    border: 1px none #000;
    border-radius: 0px;

    background-color: white;
    display: block;
    width: 100%;
    height: 38px;
    padding: 8px 12px;

    font-size: 14px;
    line-height: 1.42857143;
    color: #000;
  }

  :global(.grid) {
    display: grid;
    grid-gap: 8px;
    align-items: center;
    grid-auto-columns: auto;
  }
  :global(.grid.grid-with-actions) {
    grid-template-columns: 1fr 1fr auto;
  }
  :global(.grid.grid-two) {
    grid-template-columns: 1fr 1fr;
  }

  :global(.button) {
    margin-right: 16px;
    padding-top: 6px;
    padding-bottom: 6px;
    padding-right: 24px;
    padding-left: 24px;
    font-size: 15px;
    line-height: 16px;
    height: auto;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    border-radius: 4px;
  }

  :global(.button.primary) {
    background-color: #4353ff;
    -webkit-transition-property: none;
    transition-property: none;
    color: #fff;
    border-radius: 4px !important;
  }
  :global(.button.primary:hover) {
    background-color: #3545ee;
    color: #fff;
  }
  :global(.button.remove) {
    background-color: #ff6382;
    border: none;
    border-radius: 50%;
    color: #fff;
    font-size: 10px;
    height: 30px;
    width: 30px;
    box-shadow: none;
    padding: 0;
        line-height: 30px;
        text-align: center;

  }
  :global(.button.remove:hover) {
    background-color: #ff6382;
    color: #fff;
  }
  :global(.button.add) {
    background-color: #38d996;
    border: none;
    border-radius: 50%;
    color: #fff;
    font-size: 10px!important;
    width: 30px;
    height: 30px;
    box-shadow: none;
  }
  :global(.button.add:hover) {
    background-color: #38d996;
    color: #fff;
  }
  :global(p.description-subtitle) {
        color: #999999;
    font-size: 12px;
  }
</style>

<Container>
  <StaticRules />
  <CollectionRules />
</Container>
