/**
 * The admin script's pieces that its compat script aliases, under a key that Object.keys() does not list.
 */
const KEY = Symbol.for('site-reviews.admin');

export const share = (pieces) => {
    if (!window.GLSR[KEY]) {
        Object.defineProperty(window.GLSR, KEY, { value: pieces })
    }
}

export const shared = () => window.GLSR?.[KEY] || null;
