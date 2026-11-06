import { adopt, config, dom, Event, feature, isAttached, lib, listen, expect, debounce, fadeIn, fadeOut, historyState, isEmpty, parseJson, selectText, throttle } from '@/public/core/index.js';
import load, { requestShim } from '@/public/bootstrap/load.js';
import { pending, scan, watch } from '@/public/bootstrap/watcher.js';

// The review that a submission or a verification link redirects to.
const openReview = () => {
    const url = new URL(location.href);
    const params = Object.fromEntries(url.searchParams);
    if (!params.review_id) return;
    const requestKeys = ['form', 'review_id', 'theme', 'verified'];
    const values = Object.fromEntries(requestKeys.filter(k => k in params).map(k => [k, params[k]]));
    requestKeys.forEach(k => url.searchParams.delete(k));
    history.replaceState(historyState(), '', url);
    load('reviews').then(() => window.GLSR.Review.open(params.review_id, values))
}

let hasEvents = false;

// The listener is added late, so that those of other scripts run before the page is set up.
const initEvents = () => {
    if (hasEvents) return;
    hasEvents = true;
    listen('site-reviews/init', () => scan(document, true))
}

window.GLSR.Event = adopt(window.GLSR.Event);
window.GLSR.lib = lib;
window.GLSR.load = load;
if (!isAttached('request')) {
    window.GLSR.Request = requestShim(); // the request module replaces it
}
// debug mode for one page view
if (config.debug?.url && new URLSearchParams(location.search).has('glsr-debug')) {
    expect()
    const script = document.createElement('script');
    script.src = config.debug.url;
    document.head.appendChild(script)
}
window.GLSR.Util = { debounce, dom, fadeIn, fadeOut, feature, historyState, isEmpty, parseJson, selectText, throttle };

// Resolves when what it set up is ready; a builder calls it by name after inserting markup.
window.GLSR_init = (first, ...args) => {
    initEvents()
    if (1 === first?.nodeType) {
        return scan(first, true);
    }
    Event.trigger((first || 'site-reviews/init'), ...args)
    return pending();
};

// Nothing imports a module before DOMContentLoaded: on a classic theme the import map prints in the footer.
const ready = () => {
    initEvents()
    watch()
    // for some reason, querySelectorAll return double the results in Firefox without this timeout...
    setTimeout(() => Event.trigger('site-reviews/init'), 5)
    setTimeout(() => openReview(), 10)
}

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', ready)
} else {
    ready()
}
