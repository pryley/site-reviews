import { registerStore, select } from '@wordpress/data';

const DEFAULT_STATE = {
    cachedOptions: {}, // Object to store cached options per endpoint
};

// Actions
const actions = {
    setOptions(endpoint, options) {
        return {
            type: 'SET_OPTIONS',
            endpoint,
            options,
        };
    },
};

// Reducer
const reducer = (state = DEFAULT_STATE, action) => {
    switch (action.type) {
        case 'SET_OPTIONS':
            return {
                ...state,
                cachedOptions: {
                    ...state.cachedOptions,
                    [action.endpoint]: action.options,
                },
            };
        default:
            return state;
    }
};

// Selectors
const selectors = {
    getOptions(state, endpoint) {
        return state.cachedOptions[endpoint] || null;
    },
};

// Register the store only if not already registered
if (!select('site-reviews')) {
    registerStore('site-reviews', { actions, reducer, selectors });
}
