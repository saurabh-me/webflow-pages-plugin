<script>
  export let value = "Submit";
  export let disabled = false;
  export let variation = null;
</script>

<style>
  input[type="submit"] {
    -webkit-appearance: button;
  }

  input[type="submit"] {
    display: inline-block;
    padding: 9px 15px;
    color: white;
    border: 0;
    line-height: inherit;
    text-decoration: none;
    cursor: pointer;
    border-radius: 4px;
  }

  .form_submit-button {
    width: 100%;
    height: 50px;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #4353ff;
    font-weight: 500;
  }

  .form_submit-button:hover {
    background-color: #3545ee;
  }
  .form_submit-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  .form_submit-button[variation="small"] {
    width: 150px;
    height: auto;
  }

  .form_submit-button[variation="black"] {
    width: auto;
    height: auto;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #333;
    font-weight: 500;
  }

  .form_submit-button[variation="black"]:hover {
    background-color: #000;
  }

  .form_submit-button[variation="red"] {
    width: auto;
    height: auto;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #ff6382;
    font-weight: 500;
  }

  .form_submit-button[variation="red"]:hover {
    background-color: #ff6382;
  }

  .form_submit-button[variation="grey"] {
    width: auto;
    height: auto;
    padding-right: 28px;
    padding-left: 28px;
    background-color: #d2d4d5;
    font-weight: 500;
  }

  .form_submit-button[variation="grey"]:hover {
    background-color: #d2d4d5;
  }
</style>

<input
  type="submit"
  {value}
  class="form_submit-button"
  {variation}
  {disabled} />
