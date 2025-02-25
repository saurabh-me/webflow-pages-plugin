<script>
    import {isCurrentPath} from '../store/router';
    
    export let component;
    export let path;

    const isActive = isCurrentPath(path);
</script>

<!-- Shows only the Active Component -->
{#if $isActive}
<svelte:component this={component} />
{/if}