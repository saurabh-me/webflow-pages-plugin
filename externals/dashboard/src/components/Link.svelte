<script>
  import { router } from "../store/router";
  import { isCurrentPath } from "../store/router";

  export let path;
  export let params = {};

  export let text = "";

  const handleClick = e => {
    e.preventDefault();
    router.goTo(path, params);
  };

  const isActive = isCurrentPath(path);
 
</script>

<style>
  a {
    height: 36px;
    margin-bottom: 0px;
    margin-right: 10px;
    padding-left: 10px;
    padding-right: 10px;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 auto;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
    -webkit-transition: color 200ms ease-in-out;
    transition: color 200ms ease-in-out;
    color: #888;
    font-size: 15px;
    line-height: 37px;
    font-weight: 500;
    text-decoration: none;
    border-bottom: 2px solid transparent;
  }
  a:hover {
    color: #000;
  }
  a.is-active {
    color: #000;
    border-bottom: 2px solid #000;
  }
  a:focus {
    outline: none;
    box-shadow: none;
  }
</style>

<a href="/" on:click={handleClick} class={$isActive ? 'is-active' : ''}>
  {text}
</a>
