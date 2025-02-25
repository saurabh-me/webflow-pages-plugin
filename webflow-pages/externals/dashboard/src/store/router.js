/**
 *  These store is rensposible of handling the "fake routing" of the application,
 *  its a routing without browser history
 *
 */
import { writable, derived } from 'svelte/store';

function createRouterStore() {
  const { subscribe, set } = writable({
    path: '/',
    params: {}
  });

  return {
    subscribe,
    // Go To the defined path with the defined params
    goTo: (path, params = {}) => set({ path, params })
  };
}

export const router = createRouterStore();

export const isCurrentPath = path =>
  derived(router, $router => $router.path === path);
