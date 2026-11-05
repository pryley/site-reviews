/**
 * A page with the BUILT bootstrap and its script modules (assets/scripts): run `make build:assets` first.
 *
 * jsdom cannot run ES modules, so the page runs in Node's own realm: the jsdom window's DOM is
 * installed as the globals a browser has, the classic scripts run with vm, and the bootstrap's
 * import() reaches the built modules through resolve-hooks.mjs. One realm, as in a browser.
 */
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { register } from 'node:module';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { JSDOM } from 'jsdom';
import harness from './harness.js';

register('./resolve-hooks.mjs', import.meta.url);

const { DIALOG, inlineScript, publicConfig, read } = harness;

const ASSETS = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../../assets/scripts');

// What the scripts reach as bare globals; each is the jsdom window's for the page being tested.
const GLOBALS = [
    'CSSStyleSheet', 'CustomEvent', 'DOMParser', 'Element', 'Event', 'FormData', 'HTMLDialogElement', 'HTMLElement',
    'HTMLFormElement', 'IntersectionObserver', 'KeyboardEvent', 'MouseEvent', 'MutationObserver', 'Node', 'NodeList', 'PopStateEvent', 'ResizeObserver',
    'XMLHttpRequest', 'document', 'getComputedStyle', 'history', 'location', 'navigator', 'requestAnimationFrame',
    'sessionStorage', 'window',
];

const original = { error: console.error, info: console.info, warn: console.warn };

let run = 0;

const define = (name, get, set) => Object.defineProperty(globalThis, name, { configurable: true, get, set });

const install = (window) => {
    GLOBALS.forEach(name => define(name, () => window[name], (value) => { window[name] = value; }))
    // the scripts call fetch and console bare, and a test replaces them on the window
    define('fetch', () => window.fetch, (value) => { window.fetch = value; })
    define('GLSR', () => window.GLSR, (value) => { window.GLSR = value; })
    define('GLSR_init', () => window.GLSR_init, (value) => { window.GLSR_init = value; })
    window.console = console;
}

const capture = () => {
    const lines = { errors: [], infos: [], warnings: [] };
    console.error = (...args) => lines.errors.push(args.join(' '));
    console.info = (...args) => lines.infos.push(args.join(' '));
    console.warn = (...args) => lines.warnings.push(args.join(' '));
    return lines;
}

export const restoreConsole = () => Object.assign(console, original);

const script = (code, filename) => vm.runInThisContext(code, {
    filename,
    importModuleDynamically: vm.constants.USE_MAIN_CONTEXT_DEFAULT_LOADER,
});

/**
 * @param {Object} options
 * @param {Object} options.added  The keys that an 8.x localize filter added
 * @param {string} options.before Code that runs before the bootstrap
 * @param {boolean} options.compat Compat mode is on: the config says so and the compat script follows
 * @param {Object} options.config Subjects that replace those of the default config
 * @param {boolean} options.debug Debug mode is on: the config says so and the debug script follows
 * @param {string} options.html   The body
 * @param {string} options.inline The whole inline script, in place of the default
 * @param {string} options.url    The page's address
 */
export const loadPage = ({ added, before = '', compat, config = {}, debug = false, html = '', inline, url = 'https://example.org/' } = {}) => {
    run++;
    compat ??= !inline;
    const dom = new JSDOM(`<!doctype html><html><body>${html}</body></html>`, { pretendToBeVisual: true, url });
    const { window } = dom;
    install(window)
    const lines = capture();
    // the first site-reviews/initialized for the page: the bootstrap has scanned it and loaded what it names
    const ready = new Promise(resolve => {
        window.GLSR = { Event: { q: [['site-reviews/initialized', ({ root }) => root === window.document && resolve()]], on (...args) { this.q.push(args) } } };
    });
    script(inline ?? inlineScript({ ...publicConfig(), ...config, compat, debug: { enabled: debug, ...config.debug } }, added), 'inline-script.js')
    script(DIALOG + before, 'before.js')
    script(read('site-reviews.js'), `${pathToFileURL(path.join(ASSETS, 'site-reviews.js')).href}?run=${run}`)
    if (compat) {
        script(read('site-reviews-compat.js'), 'site-reviews-compat.js')
    }
    if (debug) {
        script(read('site-reviews-debug.js'), 'site-reviews-debug.js')
    }
    return { ...lines, read, ready, window };
};

// Run a built script, or a snippet, on the page as a classic script tag would.
export const evaluate = (code) => script(code, 'snippet.js');

export { publicConfig };

export const settle = (ms = 0) => new Promise(resolve => setTimeout(resolve, ms));
