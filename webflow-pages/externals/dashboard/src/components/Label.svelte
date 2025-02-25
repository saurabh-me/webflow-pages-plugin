<script>
  export let forHtml = "";
</script>

<style>
  .field_label {
    margin-top: 6px;
    margin-bottom: 6px;
    color: #999999;
    font-size: 12px;
    line-height: 18px;
    font-weight: bold;
    display: block;
  }
</style>

<label class="field_label" for={forHtml}>
  <slot />
</label>
