/**
 * The stylesheets a root or partial names in data-glsr-styles. The server prints them beside the
 * first root of a page render; a fragment rendered in its own request (a page of reviews, a builder's
 * canvas re-rendering an element) carries only the names, and the bootstrap adds the links the page lacks.
 */
import config from './config.js';

export const SELECTOR = '[data-glsr-styles]';

// the once-per-page set that every component's rules depend on, as links when the page lacks them
const SHARED = ['variables', 'keyframes', 'base', 'loaders'];

const id = (component) => `glsr-css-${component}`;

const has = (component) => null !== document.getElementById(id(component));

const link = (component, href) => {
    const el = document.createElement('link');
    el.id = id(component);
    el.rel = 'stylesheet';
    el.href = href;
    return el;
}

/**
 * Add a link for each component the page does not have, from the files the config names.
 */
export const ensure = (components) => {
    const urls = config.styles || {};
    const wanted = has('shared') ? components : [...SHARED, ...components];
    wanted.forEach((component) => {
        if (!has(component) && urls[component]) {
            document.head.appendChild(link(component, urls[component]))
        }
    })
}

/**
 * Ensure the components named on an element and on the elements inside it.
 */
export const declared = (root) => {
    const elements = [...root.querySelectorAll(SELECTOR)];
    if (root.matches?.(SELECTOR)) {
        elements.unshift(root)
    }
    const components = elements.flatMap(el => (el.dataset.glsrStyles || '').split(/\s+/).filter(Boolean));
    if (components.length) {
        ensure([...new Set(components)])
    }
}
