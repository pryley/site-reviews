/**
 * Find the roots that name a module (data-glsr-modules="form captcha") and set each up with its modules,
 * on the first scan and whenever one arrives later.
 */

import { Event, declaredStyles, direction, load, setup as setupModule, STYLES_SELECTOR } from '@/public/core/index.js';

const SELECTOR = '[data-glsr-modules]';

const tokens = (root) => (root.dataset.glsrModules || '').split(/\s+/).filter(Boolean);

const roots = (root) => {
    const found = [...root.querySelectorAll(SELECTOR)];
    if (root.matches?.(SELECTOR)) {
        found.unshift(root)
    }
    return found;
}

// every wrapper gets the class, including those of features that do not set it themselves
const directions = (root) => {
    const wrappers = [...root.querySelectorAll('.glsr')];
    if (root.matches?.('.glsr')) {
        wrappers.push(root)
    }
    wrappers.forEach(direction)
}

/**
 * Set a root up with each module it names, in the order it names them.
 */
export const setup = (root, force = false) => tokens(root).reduce(
    (previous, token) => previous.then(() => load(token)).then(() => setupModule(token, root, force)).catch(error => {
        console.error(`[site-reviews] The module "${token}" could not be loaded.`, error)
    }),
    Promise.resolve()
);

let latest = Promise.resolve();

// The scan that is running, or the last one: GLSR_init() returns it.
export const pending = () => latest

/**
 * Set up every root inside an element, or the page.
 */
export const scan = (root = document, force = false) => {
    declaredStyles(root)
    directions(root)
    latest = Promise.all(roots(root).map(el => setup(el, force))).then(() => {
        Event.trigger('site-reviews/initialized', { root }) // this goes last!
    });
    return latest;
}

export const watch = () => {
    const observer = new MutationObserver(mutations => {
        mutations.forEach(({ addedNodes }) => {
            addedNodes.forEach(node => {
                if (1 !== node.nodeType) return;
                if (roots(node).length) {
                    scan(node)
                } else {
                    declaredStyles(node) // a rating on its own names its stylesheet
                    directions(node) // a wrapper with no module, such as a WooCommerce rating
                }
            })
        })
    });
    observer.observe(document.documentElement, { childList: true, subtree: true })
    return observer;
}
