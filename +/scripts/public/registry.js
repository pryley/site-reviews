// shared with the other script when both are on a page
const KEY = Symbol.for('site-reviews.registry');

const state = window.GLSR?.registry?.[KEY] || {
    factories: {},
    hosted: {},
    isBooted: false,
    loading: {},
    order: [],
    ran: new Set(),
    urls: {},
};

const { factories, hosted, loading, order, ran, urls } = state;

const run = (id) => {
    if (ran.has(id)) return;
    ran.add(id)
    // handed over once, and not kept where another script could read them
    const pieces = hosted[id];
    delete hosted[id];
    try {
        factories[id](pieces)
    } catch (error) {
        console.error(`[site-reviews] The script "${id}" failed.`, error)
    }
}

const boot = () => {
    if (state.isBooted) return;
    state.isBooted = true;
    order.forEach(run)
}

const define = (id, factory) => {
    if (id in factories) return;
    factories[id] = factory;
    order.push(id)
    if (state.isBooted || id in hosted) {
        run(id)
    }
}

const load = (id) => {
    if (id in factories) {
        return Promise.resolve();
    }
    if (!loading[id]) {
        loading[id] = new Promise((resolve, reject) => {
            const fail = () => {
                delete loading[id];
                reject(new Error(id))
            }
            if (!urls[id]) {
                return fail();
            }
            const script = document.createElement('script');
            script.onerror = fail;
            script.onload = () => id in factories ? resolve() : fail();
            script.src = urls[id];
            document.head.appendChild(script)
        });
    }
    return loading[id];
}

const register = (entries) => {
    Object.entries(entries || {}).forEach(([id, url]) => {
        if (!(id in urls)) {
            urls[id] = url;
        }
    })
}

export const defined = () => order.map(id => ({ id, ran: ran.has(id) }));

// The factory of `id` runs as soon as it is defined, with `pieces` that are not reachable from GLSR.
export const host = (id, pieces) => {
    hosted[id] = pieces;
}

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', boot)
} else {
    boot()
}

const registry = { boot, define, load, register };

Object.defineProperty(registry, KEY, { value: state })

export default window.GLSR?.registry?.[KEY] ? window.GLSR.registry : registry
