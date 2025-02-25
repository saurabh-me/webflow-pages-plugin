import { writable } from 'svelte/store';

function createNotificationStore() {
    const { subscribe, update } = writable({
        currentNotification: null
      });
    
      return {
        subscribe,
        // Go To the defined path with the defined params
        addNotification: (message, type = "success") => {
            update( data => {
                data.currentNotification = {message, type};
                return {...data}
            });
            setTimeout(() => {
                update( data => {
                    data.currentNotification = null;
                    return {...data}
                })
            }, 5000)
        }
      };
}

export const notifications = createNotificationStore();
