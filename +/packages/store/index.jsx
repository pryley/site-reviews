import { createReduxStore, register, select } from '@wordpress/data';

// create and register a data store
export const createStore = (storeName) => {
    // Check if the store is not already registered before creating and registering it
    if (!select(storeName)) {
        const store = createReduxStore(storeName, {
            actions: {
                set: (key, endpoint, values) => ({
                    type: 'SET',
                    key,
                    endpoint,
                    values,
                }),
            },
            controls: {},
            reducer: (state = {}, action) => {
                if (action.type === 'SET') {
                    return {
                        ...state,
                        [action.key]: {
                            ...state[action.key],
                            [action.endpoint]: action.values,
                        },
                    };
                }
                return state;
            },
            selectors: {
                get: (state, key, endpoint) => state[key]?.[endpoint] || [],
            },
        });

        register(store);
    }

    return storeName;
};

// Define and export the default store name
export const DEFAULT_STORE_NAME = 'site-reviews';

export default createStore;
