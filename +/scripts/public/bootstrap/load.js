/**
 * GLSR.load(token) and the GLSR.Request shim: the globals a code snippet calls before a module has loaded.
 */
import { load } from '@/public/core/index.js';

/**
 * Resolve once the module is on the page.
 */
export default load

/**
 * Build the GLSR.Request a page has before the request module: each method loads the module and
 * calls through, and every one returns a promise.
 */
export const requestShim = () => Object.fromEntries(
    ['log', 'pagedReviews', 'review', 'send', 'submit'].map(name => [
        name,
        (...args) => load('request').then(() => window.GLSR.Request[name](...args)),
    ])
);
