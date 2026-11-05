import config from './config.js';

const LIMIT = 50;

// shared with the other copies of this file on a page: the debug script attaches to one of them
const KEY = Symbol.for('site-reviews.report');

window.GLSR = window.GLSR || {};

const state = window.GLSR[KEY] || {
    isExpected: true === config.debug?.enabled,
    listener: null,
    pending: [],
};

if (!window.GLSR[KEY]) {
    Object.defineProperty(window.GLSR, KEY, { value: state })
}

export const attach = (fn) => {
    state.listener = fn;
    state.pending.splice(0).forEach(([type, data]) => report(type, data))
}

// reports are kept only while the debug script is on its way
export const expect = () => {
    state.isExpected = true;
}

const report = (type, data) => {
    if (state.listener) {
        try {
            state.listener(type, data)
        } catch (error) {} // debug mode never changes what the scripts do
    } else if (state.isExpected && state.pending.length < LIMIT) {
        state.pending.push([type, data])
    }
}

export default report
