import apiFetch from '@wordpress/api-fetch';
import createStore, { DEFAULT_STORE_NAME } from '@site-reviews/store';
import { dispatch } from '@wordpress/data';
import { toOption } from './utils';

interface Item {
    id: string | number;
    title: string;
}

/**
 * Warms the shared options cache used by AjaxComboboxControl and
 * AjaxToggleGroupControl. Call at app startup with the endpoints of
 * controls that render inside lazily-mounted containers (PanelBody
 * accordions, block InspectorControls). Required for controls using
 * hideIfEmpty — they otherwise pop into existence after their request
 * resolves on first panel open — and beneficial for the rest, which
 * otherwise show a brief loading state; the same requests fire either
 * way, warming just moves them to startup.
 *
 * The endpoint strings are the cache keys and must match the endpoint
 * props passed to the controls exactly. Failures are silent — the
 * control falls back to fetching for itself.
 *
 * Note: this deliberately does NOT warm the suggestedValues cache used
 * by AjaxFormTokenField — its initValues() resolves the SELECTED values
 * from cached suggestions instead of fetching them (with include=), so
 * a warmed generic list that lacks the saved ids would drop selected
 * tokens from display.
 */
export const prefetchOptions = (endpoints: string[], storeName: string = DEFAULT_STORE_NAME): void => {
    const registeredStoreName = createStore(storeName);
    endpoints.forEach((endpoint) => {
        apiFetch<Item[]>({ path: endpoint })
            .then((items) => {
                dispatch(registeredStoreName).set('options', endpoint, items.map(toOption));
            })
            .catch(() => {});
    });
};

export default prefetchOptions;
