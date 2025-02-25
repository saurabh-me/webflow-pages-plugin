<script>
    import {notifications} from '../store/notifications';
    import { fade } from 'svelte/transition';
   
</script>
<style>
div {
    position: fixed;
    bottom: 40px;
    left: 55%;
    padding: 8px 100px;
    transform: translateX(-50%);
}
p {
    margin: 0;
    color: white;
    font-size: 14px;
    text-align: center;
}
</style>
{#if $notifications.currentNotification}
<div transition:fade style={$notifications.currentNotification.type == "success" ? "background-color: #38D996" : "background-color:  #FF6382"}>
    <p>{$notifications.currentNotification.message}</p>
</div>
{/if}