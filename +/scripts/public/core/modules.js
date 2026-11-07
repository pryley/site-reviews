/**
 * The modules a page can name on a root (data-glsr-modules="form captcha") and the roots each has set up.
 * The bootstrap and every module share this state, so a module that attaches on evaluation and
 * the watcher that finds a root later never set the same root up twice.
 */

// shared with the other copies of this file on a page
const KEY = Symbol.for('site-reviews.modules');

window.GLSR = window.GLSR || {};

const state = window.GLSR[KEY] || {
    attached: {},  // token => init(root)
    done: {},      // token => the roots it has set up
    handed: {},    // token => what the module handed over for the compat script
    importer: (token) => import(`@site-reviews/${token}`),
    loading: {},   // token => the promise of its import
    ready: {},     // token => the callbacks waiting for attach()
};

if (!window.GLSR[KEY]) {
    Object.defineProperty(window.GLSR, KEY, { value: state })
}

const { attached, done, handed, loading, ready } = state;

/**
 * Register a module's init(root) and the internals the compat script reads, which are not
 * reachable from GLSR; a module calls this once, as it evaluates.
 */
export const attach = (token, init, pieces = {}) => {
    if (attached[token]) return;
    attached[token] = init;
    handed[token] = pieces;
    (ready[token] || []).splice(0).forEach(fn => fn(pieces))
}

export const isAttached = (token) => token in attached;

/**
 * Resolve when the module of a token has attached; import it when nothing has.
 */
export const load = (token) => {
    if (attached[token]) {
        return Promise.resolve();
    }
    if (!loading[token]) {
        loading[token] = new Promise((resolve, reject) => {
            (ready[token] || (ready[token] = [])).push(resolve)
            state.importer(token).catch(error => {
                delete loading[token];
                reject(error)
            })
        });
    }
    return loading[token];
}

// the tests replace the import() call
export const setImporter = (fn) => {
    state.importer = fn;
}

/**
 * Resolve with what a module handed over once it has attached, without loading it.
 */
export const whenAttached = (token) => {
    if (attached[token]) {
        return Promise.resolve(handed[token]);
    }
    return new Promise(resolve => (ready[token] || (ready[token] = [])).push(resolve));
}

/**
 * Set a root up with one module, once. `force` sets it up again (GLSR_init).
 */
export const setup = (token, root, force = false) => {
    const roots = done[token] || (done[token] = new WeakSet());
    if (!force && roots.has(root)) return;
    roots.add(root)
    try {
        attached[token](root)
    } catch (error) {
        console.error(`[site-reviews] The module "${token}" failed.`, error)
    }
}
