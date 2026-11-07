/**
 * The key used to signify that the admin script has run
 */
const KEY = Symbol.for('site-reviews.admin');

export const share = () => {
    if (!window.GLSR[KEY]) {
        Object.defineProperty(window.GLSR, KEY, { value: true })
    }
}

export const shared = () => true === window.GLSR?.[KEY];
