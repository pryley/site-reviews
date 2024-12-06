import { createReduxStore, register, select } from '@wordpress/data';

const STORE_NAME = 'site-reviews';

// Check if the store is not already registered before creating and registering it
if (!select(STORE_NAME)) {
    const initialState = {
        options: {},
        selectedValues: {},
        suggestedValues: {},
    };

    const actions = {
        setOptions: (endpoint, options) => {
            return {
                type: 'SET_OPTIONS',
                endpoint,
                options,
            };
        },
        setSelectedValues: (endpoint, selectedValues) => {
            return {
                type: 'SET_SELECTED_VALUES',
                endpoint,
                selectedValues,
            };
        },
        setSuggestedValues: (endpoint, suggestedValues) => {
            return {
                type: 'SET_SUGGESTED_VALUES',
                endpoint,
                suggestedValues,
            };
        },
    };

    const reducer = (state = initialState, action) => {
        switch (action.type) {
            case 'SET_OPTIONS':
                return {
                    ...state,
                    options: {
                        ...state.options,
                        [action.endpoint]: action.options,
                    },
                };
            case 'SET_SELECTED_VALUES':
                return {
                    ...state,
                    selectedValues: {
                        ...state.selectedValues,
                        [action.endpoint]: action.selectedValues,
                    },
                };
            case 'SET_SUGGESTED_VALUES':
                return {
                    ...state,
                    suggestedValues: {
                        ...state.suggestedValues,
                        [action.endpoint]: action.suggestedValues,
                    },
                };
            default:
                return state;
        }
    };

    const selectors = {
        getOptions: (state, endpoint) => state.options[endpoint] || [],
        getSelectedValues: (state, endpoint) => state.selectedValues[endpoint] || [],
        getSuggestedValues: (state, endpoint) => state.suggestedValues[endpoint] || [],
    };

    const store = createReduxStore(STORE_NAME, {
        reducer,
        actions,
        selectors,
        controls: {},
    });

    register(store);
}

export default STORE_NAME;
