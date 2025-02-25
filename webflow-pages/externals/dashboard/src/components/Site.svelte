<script>
  import {store} from '../store/wfData';

</script>

<style>
  img {
    max-width: 100%;
    height: auto;
  }
  a {box-shadow: none}
</style>

<div>
  <a href={$store.site.dashboardUrl} target="_blank">
    <img src={$store.site.previewUrl} alt="site preview" />
    <h3>{$store.site.name}</h3>
  </a>
</div>
